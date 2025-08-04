<?php

/**
 * Mega API Helper class file.
 * @author Francesco Colamonici <francesco.colamonici@accenture.com>
 * @copyright Copyright &copy; Francesco Colamonici 2013-
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version 1.0.0
 */
require Yii::getPathOfAlias('mega.helper') . DIRECTORY_SEPARATOR . 'rsa.php';
require Yii::getPathOfAlias('mega.helper') . DIRECTORY_SEPARATOR . 'crypt.php';

abstract class MegaHelper extends CApplicationComponent {

    private static $schema = 'https';
    private static $domain = 'mega.co.nz';
    private static $timeout = 60; // seconds
    protected static $master_key = array(0, 0, 0, 0);
    protected static $rsa_private_key = array(0, 0, 0, 0);
//    protected static $master_key = array(827189518, 522087148, 1778652391, 398466391);
//    protected static $sid = '8oG09y_dK0GmiVcI2XfFBGxlQ2xPUTl3WDJrGisulePnGwQIhbtU-rWEYA';
    protected static $sid = null;
    protected static $root_id = null;
    protected static $inbox_id = null;
    protected static $trashbin_id = null;
    protected static $sequence_num = 0;
    protected static $request_id = '';

    public function init() {
        self::$request_id = substr(uniqid(), 0, 10);
        self::$sequence_num = rand();
    }

    protected function login_user($email, $password) {
        $password_aes = prepare_key(str_to_a32($password));
        $uh = stringhash(strtolower($email), $password_aes);
        $resp = $this->api_request(array('a' => 'us', 'user' => $email, 'uh' => $uh));
        // if numeric error code response
        if (is_int($resp))
            throw new CHttpException($resp);
        $this->login_process($resp, $password_aes);
    }

    protected function login_anonymous() {
        $master_key = rand() * 4;
        $password_key = rand() * 4;
        $session_self_challenge = rand() * 4;

        $user = $this->api_request(array(
            'a' => 'up',
            'k' => a32_to_base64(encrypt_key($master_key, $password_key)),
            'ts' => base64_url_encode(a32_to_str($session_self_challenge) +
                    a32_to_str(encrypt_key($session_self_challenge, $master_key)))
        ));

        $resp = $this->api_request(array('a' => 'us', 'user' => $user));
        // if numeric error code response
        if (is_int($resp))
            throw new CHttpException($resp);
        $this->login_process($resp, $password_key);
    }

    private function login_process($resp, $password) {
        $encrypted_master_key = base64_to_a32($resp->k);
        self::$master_key = decrypt_key($encrypted_master_key, $password);
        if (!empty($resp->tsid)) { // anonymous
            $tsid = base64_url_decode($resp->tsid);
            $key_encrypted = a32_to_str(
                    encrypt_key(str_to_a32(substr($tsid, 0, 16)), self::$master_key));
            if ($key_encrypted == substr($tsid, -16))
                self::$sid = $resp->tsid;
        } elseif (!empty($resp->csid)) {
            $encrypted_rsa_private_key = base64_to_a32($resp->privk);
            $rsa_private_key = decrypt_key($encrypted_rsa_private_key, self::$master_key);

            $private_key = a32_to_str($rsa_private_key);
            self::$rsa_private_key = array(0, 0, 0, 0);

            for ($i = 0; $i < 4; $i++) {
                $l = ((ord($private_key[0]) * 256 + ord($private_key[1]) + 7) / 8) + 2;
                self::$rsa_private_key[$i] = mpi2bc(substr($private_key, 0, $l));
                $private_key = substr($private_key, $l);
            }

            $encrypted_sid = mpi2bc(base64_url_decode($resp->csid));
            self::$sid = rsa_decrypt($encrypted_sid, self::$rsa_private_key[0], self::$rsa_private_key[1], self::$rsa_private_key[2]);
            self::$sid = base64urlencode(substr(strrev(self::$sid), 0, 43));
        }
    }

    protected function api_request($request) {
        $this->init();
        $url = self::$schema . '://g.api.' . self::$domain . '/cs';
        $url .= '?id=' . self::$sequence_num++;
        if (!empty(self::$sid))
            $url .= '&sid=' . self::$sid;
        $resp = $this->post($url, json_encode(array($request)));
        $j_resp = json_decode($resp);
        // if numeric error code response
        if (is_int($j_resp))
            throw new CHttpException($j_resp);
        return $j_resp[0];
    }

    protected function post($url, $data) {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, self::$timeout);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $resp = curl_exec($ch);
        curl_close($ch);
        return $resp;
    }

    protected function process_file($file) {
        if ($file->t == 0 || $file->t == 1) {
            $key = substr($file->k, strpos($file->k, ':') + 1);
            $key = decrypt_key(base64_to_a32($key), self::$master_key);
            if ($file->t == 0) {
                $k = array($key[0] ^ $key[4], $key[1] ^ $key[5], $key[2] ^ $key[6], $key[3] ^ $key[7]);
                $file->iv = array_merge(array_slice($key, 4, 2), array(0, 0));
                $file->meta_mac = array_slice($key, 6, 2);
            } else {
                $k = $key;
            }
            $file->key = $key;
            $file->k = $k;
            $enc_attributes = base64urldecode($file->a);
            $attributes = $this->dec_attr($enc_attributes, $k);
            $file->a = $attributes;
        } elseif ($file->t == 2) {
            self::$root_id = $file->h;
            $file->a = array('n' => 'Cloud Drive');
        } elseif ($file->t == 3) {
            self::$inbox_id = $file->h;
            $file->a = array('n' => 'Inbox');
        } elseif ($file->t == 4) {
            self::$trashbin_id = $file->h;
            $file->a = array('n' => 'Rubbish Bin');
        }
        return $file;
    }

    protected function enc_attr($attr, $key) {
        $attr = 'MEGA' . json_encode($attr);
        return aes_cbc_encrypt($attr, a32_to_str($key));
    }

    protected function dec_attr($attr, $key) {
        $attr = trim(aes_cbc_decrypt($attr, a32_to_str($key)));
        if (substr($attr, 0, 6) != 'MEGA{"') {
            return false;
        }
        return json_decode(substr($attr, 4));
    }

}

?>
