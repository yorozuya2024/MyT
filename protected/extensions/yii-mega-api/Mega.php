<?php

/**
 * Mega API class file.
 * @author Francesco Colamonici <francesco.colamonici@accenture.com>
 * @copyright Copyright &copy; Francesco Colamonici 2013-
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version 1.0.0
 */
Yii::setPathOfAlias('mega', dirname(__FILE__));
Yii::import('mega.MegaGetter', true);

class Mega extends MegaGetter {

    public function init() {
        parent::init();
        if (isset(Yii::app()->params['attachments']) && Yii::app()->params['attachments']['storage'] === 'mega') {
            self::$master_key = unserialize(Yii::app()->params['attachments']['apiKey']);
            self::$sid = Yii::app()->params['attachments']['apiSID'];
        }
    }

    public function login($email = null, $password = null) {
        $this->init();
        Yii::log('MEGA: logging in... ' . $email . ' / ' . $password);
        if ($email === null)
            parent::login_anonymous();
        else
            parent::login_user($email, $password);
    }

    public function deleteFile($filename) {
        $file = parent::find($filename);
        if ($file === null)
            throw new CHttpException(405);
        return $this->moveFile($file->h, 4);
    }

    public function destroyFile($file_id) {
        return parent::api_request(array(
                    'a' => 'd',
                    'n' => $file_id,
                    'i' => parent::$request_id
                        )
        );
    }

    public function downloadFile($filename) {
        $file = parent::find($filename);
        if ($file === null)
            throw new CHttpException(405);
        $this->download($file);
    }

    private function download($file) {
        $file_data = parent::api_request(array('a' => 'g', 'g' => 1, 'n' => $file->h));
        $k = $file->k;
        $iv = $file->iv;
        $meta_mac = $file->meta_mac;
        if (empty($file_data->g))
            throw new CHttpException(204, 'File not accessible anymore');
        $file_url = $file_data->g;
        $file_size = $file_data->s;
        $enc_attributes = base64urldecode($file_data->at);
        $attributes = parent::dec_attr($enc_attributes, $k);
        $file_name = $attributes->n;

        // Download
        Yii::log('MEGA: downloading file... ' . $attributes->n);
        ob_start();
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        header('Content-type: ' . CFileHelper::getMimeTypeByExtension($file_name));
        header('Content-Disposition: attachment; filename="' . $file_name . '"');
        header('Cache-Control: max-age=0');

        $r = fopen($file_url, 'rb');
        $w = fopen('php://output', 'wb');

        // $size = 16; // ?
        $chunks = get_chunks($file_size);
        ksort($chunks);

        $data = '';
        foreach ($chunks as $start => $end) {
            $enc_data = stream_get_contents($r, $end);
            $dec_data = aes_ctr_decrypt($enc_data, a32_to_str($k), a32_to_str($iv));
            $data .= $dec_data;
            fwrite($w, $dec_data);
        }

        $file_mac = cbc_mac($data, $k, $iv);
        if (array($file_mac[0] ^ $file_mac[1], $file_mac[2] ^ $file_mac[3]) != $meta_mac) {
            ob_end_clean();
            throw new CHttpException(403, 'MAC mismatch');
        }

        fclose($w);
        fclose($r);
        ob_end_flush();
        Yii::app()->end();
    }

    public function uploadFile($filename, $dest_filename = null, $dest_folder = null) {
        // determine storage node
        if ($dest_folder === null) {
            if (parent::$root_id === null)
                parent::get_files();
            $dest_folder = parent::$root_id;
        }
        if ($dest_filename === null)
            $dest_filename = $filename;

        $data = file_get_contents($filename);
        $size = strlen($data);
        $ul_url = parent::api_request(array('a' => 'u', 's' => $size));
        $ul_url = $ul_url->p;

        $ul_key = array(0, 1, 2, 3, 4, 5);
        for ($i = 0; $i < 6; $i++) {
            $ul_key[$i] = rand(0, 0xFFFFFFFF);
        }

        $data_crypted = aes_ctr_encrypt($data, a32_to_str(array_slice($ul_key, 0, 4)), a32_to_str(array($ul_key[4], $ul_key[5], 0, 0)));
        $completion_handle = parent::post($ul_url, $data_crypted);

        $data_mac = cbc_mac($data, array_slice($ul_key, 0, 4), array_slice($ul_key, 4, 2));
        $meta_mac = array($data_mac[0] ^ $data_mac[1], $data_mac[2] ^ $data_mac[3]);
        $attributes = array('n' => basename($dest_filename));
        $enc_attributes = parent::enc_attr($attributes, array_slice($ul_key, 0, 4));
        $key = array($ul_key[0] ^ $ul_key[4], $ul_key[1] ^ $ul_key[5], $ul_key[2] ^ $meta_mac[0], $ul_key[3] ^ $meta_mac[1], $ul_key[4], $ul_key[5], $meta_mac[0], $meta_mac[1]);
        return parent::api_request(array(
                    'a' => 'p',
                    't' => $dest_folder,
                    'n' => array(
                        array(
                            'h' => $completion_handle,
                            't' => 0,
                            'a' => base64urlencode($enc_attributes),
                            'k' => a32_to_base64(encrypt_key($key, parent::$master_key))
                        )
                    )
                        )
        );
    }

    /**
     * Move a file to another parent node
     *  params:
     *  a : command
     *  n : node we're moving
     *  t : id of target parent node, moving to
     *  i : request id
     *
     *  targets
     *  2 : root
     *  3 : inbox
     *  4 : trash
     *
     *  or...
     *  target's id
     *  or...
     *  target's structure returned by find()
     * @param type $file_id
     * @param mixed $target
     */
    public function moveFile($file_id, $target) {
        $target_node_id = '';
        if (is_int($target)) {
            $node = parent::get_node_by_type($target);
            $target_node_id = '' . $node->h;
        } elseif (is_string($target)) {
            $target_node_id = $target;
        } else {
            $file = $target[1];
            $target_node_id = $file->h;
        }
        CVarDumper::dump(array(
            'a' => 'm',
            'n' => $file_id,
            't' => $target_node_id,
//                    'i' => parent::$request_id,
        ));
        return parent::api_request(array(
                    'a' => 'm',
                    'n' => $file_id,
                    't' => $target_node_id,
//                    'i' => parent::$request_id,
        ));
    }

    public function createFolder($name, $dest = null) {
        if ($dest === null) {
            if (parent::$root_id === null)
                parent::get_files();
            $dest = parent::$root_id;
        }
        $ul_key = array(0, 1, 2, 3, 4, 5);
        for ($i = 0; $i < 6; $i++) {
            $ul_key[$i] = rand(0, 0xFFFFFFFF);
        }
        $attributes = array('n' => $name);
        $enc_attributes = base64urlencode(parent::enc_attr($attributes, array_slice($ul_key, 0, 4)));
        $enc_key = a32_to_base64(encrypt_key(array_slice($ul_key, 0, 4), parent::$master_key));
        return parent::api_request(array(
                    'a' => 'p',
                    't' => $dest,
                    'n' => array(
                        array(
                            'h' => 'xxxxxxxx',
                            't' => 1,
                            'a' => $enc_attributes,
                            'k' => $enc_key
                        )
                    )
//                    'i' => parent::$request_id,
        ));
    }

    public function rename($filename, $name) {
        $file = parent::find($filename);
        if ($file === null)
            throw new CHttpException(405);

        $attributes = array('n' => $name);
        $enc_attributes = base64urlencode(parent::enc_attr($attributes, $file->k));
        $enc_key = a32_to_base64(encrypt_key($file->key, parent::$master_key));
        return parent::api_request(array(
                    'a' => 'a',
                    'attr' => $enc_attributes,
                    'key' => $enc_key,
                    'n' => $file->h,
//                    'i' => parent::$request_id,
                        )
        );
    }

    public function test() {
        CVarDumper::dump(parent::get_files());
        $node = parent::get_node_by_id('Ig0QkQjI');
        CVarDumper::dump(parent::get_node_id($node));
    }

}

?>
