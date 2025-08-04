<?php

/**
 * @link http://julien-marchand.fr/blog/using-the-mega-api-with-php-examples/
 */
$sid = '';
$seqno = rand(0, 0xFFFFFFFF);

$master_key = '';
$rsa_priv_key = '';

//include Yii::app()->basePath . '/views/site/pages/_crypt.php';

//function api_req($req) {
//    global $seqno; // $sid;
//    $resp = post('https://g.api.mega.co.nz/cs?id=' . ($seqno++) . (Mega::$sid ? '&sid=' . Mega::$sid : ''), json_encode(array($req)));
//    $resp = json_decode($resp);
//    return $resp[0];
//}
//
//function post($url, $data) {
//    $ch = curl_init($url);
//    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//    curl_setopt($ch, CURLOPT_POST, true);
//    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
//    $resp = curl_exec($ch);
//    curl_close($ch);
//    return $resp;
//}

/*
  function login($email, $password) {
  global $sid, $master_key, $rsa_priv_key;
  $password_aes = prepare_key(str_to_a32($password));
  $uh = stringhash(strtolower($email), $password_aes);
  $res = api_req(array('a' => 'us', 'user' => $email, 'uh' => $uh));

  $enc_master_key = base64_to_a32($res->k);
  $master_key = decrypt_key($enc_master_key, $password_aes);
  if (!empty($res->csid)) {
  $enc_rsa_priv_key = base64_to_a32($res->privk);
  $rsa_priv_key = decrypt_key($enc_rsa_priv_key, $master_key);

  $privk = a32_to_str($rsa_priv_key);
  $rsa_priv_key = array(0, 0, 0, 0);

  for ($i = 0; $i < 4; $i++) {
  $l = ((ord($privk[0]) * 256 + ord($privk[1]) + 7) / 8) + 2;
  $rsa_priv_key[$i] = mpi2bc(substr($privk, 0, $l));
  $privk = substr($privk, $l);
  }

  $enc_sid = mpi2bc(base64urldecode($res->csid));
  $sid = rsa_decrypt($enc_sid, $rsa_priv_key[0], $rsa_priv_key[1], $rsa_priv_key[2]);
  $sid = base64urlencode(substr(strrev($sid), 0, 43));
  }
  echo PHP_EOL, 'Master Key: '; var_dump($master_key);
  echo PHP_EOL, 'SID: '; var_dump($sid);
  echo PHP_EOL, 'RSA Private Key: '; var_dump($rsa_priv_key);
  }
 */

//function enc_attr($attr, $key) {
//    $attr = 'MEGA' . json_encode($attr);
//    return aes_cbc_encrypt($attr, a32_to_str($key));
//}
//
//function dec_attr($attr, $key) {
//    $attr = trim(aes_cbc_decrypt($attr, a32_to_str($key)));
//    if (substr($attr, 0, 6) != 'MEGA{"') {
//        return false;
//    }
//    return json_decode(substr($attr, 4));
//}

//function get_chunks($size) {
//    $chunks = array();
//    $p = $pp = 0;
//
//    for ($i = 1; $i <= 8 && $p < $size - $i * 0x20000; $i++) {
//        $chunks[$p] = $i * 0x20000;
//        $pp = $p;
//        $p += $chunks[$p];
//    }
//
//    while ($p < $size) {
//        $chunks[$p] = 0x100000;
//        $pp = $p;
//        $p += $chunks[$p];
//    }
//
//    $chunks[$pp] = ($size - $pp);
//    if (!$chunks[$pp]) {
//        unset($chunks[$pp]);
//    }
//
//    return $chunks;
//}
//
//function cbc_mac($data, $k, $n) {
//    $padding_size = (strlen($data) % 16) == 0 ? 0 : 16 - strlen($data) % 16;
//    $data .= str_repeat("\0", $padding_size);
//
//    $chunks = get_chunks(strlen($data));
//    $file_mac = array(0, 0, 0, 0);
//
//    foreach ($chunks as $pos => $size) {
//        $chunk_mac = array($n[0], $n[1], $n[0], $n[1]);
//        for ($i = $pos; $i < $pos + $size; $i += 16) {
//            $block = str_to_a32(substr($data, $i, 16));
//            $chunk_mac = array($chunk_mac[0] ^ $block[0], $chunk_mac[1] ^ $block[1], $chunk_mac[2] ^ $block[2], $chunk_mac[3] ^ $block[3]);
//            $chunk_mac = aes_cbc_encrypt_a32($chunk_mac, $k);
//        }
//        $file_mac = array($file_mac[0] ^ $chunk_mac[0], $file_mac[1] ^ $chunk_mac[1], $file_mac[2] ^ $chunk_mac[2], $file_mac[3] ^ $chunk_mac[3]);
//        $file_mac = aes_cbc_encrypt_a32($file_mac, $k);
//    }
//
//    return $file_mac;
//}

//function uploadfile($filename) {
//    global $master_key, $root_id;
//
//    $data = file_get_contents($filename);
//    $size = strlen($data);
//    $ul_url = api_req(array('a' => 'u', 's' => $size));
//    $ul_url = $ul_url->p;
//
//    $ul_key = array(0, 1, 2, 3, 4, 5);
//    for ($i = 0; $i < 6; $i++) {
//        $ul_key[$i] = rand(0, 0xFFFFFFFF);
//    }
//
//    $data_crypted = aes_ctr_encrypt($data, a32_to_str(array_slice($ul_key, 0, 4)), a32_to_str(array($ul_key[4], $ul_key[5], 0, 0)));
//    $completion_handle = post($ul_url, $data_crypted);
//
//    $data_mac = cbc_mac($data, array_slice($ul_key, 0, 4), array_slice($ul_key, 4, 2));
//    $meta_mac = array($data_mac[0] ^ $data_mac[1], $data_mac[2] ^ $data_mac[3]);
//    $attributes = array('n' => basename($filename));
//    $enc_attributes = enc_attr($attributes, array_slice($ul_key, 0, 4));
//    $key = array($ul_key[0] ^ $ul_key[4], $ul_key[1] ^ $ul_key[5], $ul_key[2] ^ $meta_mac[0], $ul_key[3] ^ $meta_mac[1], $ul_key[4], $ul_key[5], $meta_mac[0], $meta_mac[1]);
//    return api_req(array('a' => 'p', 't' => Mega::$root_id, 'n' => array(array('h' => $completion_handle, 't' => 0, 'a' => base64urlencode($enc_attributes), 'k' => a32_to_base64(encrypt_key($key, Mega::$master_key))))));
//}

//function downloadfile($file, $attributes, $k, $iv, $meta_mac) {
//    ob_end_clean();
//    ob_start();
//    $dl_url = api_req(array('a' => 'g', 'g' => 1, 'n' => $file->h));
//    $r = fopen($dl_url->g, 'rb');
//    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
//    header('Pragma: public');
//    header('Content-type: ' . 'text/plain');
//    header('Content-Disposition: attachment; filename="' . $attributes->n . '"');
//    header('Cache-Control: max-age=0');
//    $w = fopen('php://output', 'wb');
//
////    $padding_size = (strlen($data) % 16) == 0 ? 0 : 16 - strlen($data) % 16;
////    $data .= str_repeat("\0", $padding_size);
//    $size = 16; // ?
//    $chunks = get_chunks($size);
//    ksort($chunks);
//
//    foreach ($chunks as $start => $end) {
//        $enc_data = stream_get_contents($r, $end);
//        $dec_data = aes_ctr_decrypt($enc_data, a32_to_str($k), a32_to_str($iv));
//        fwrite($w, $dec_data);
//    }
//
//    fclose($w);
//    fclose($r);
//    ob_end_flush();
//}

//function downloadfileORIG($file, $attributes, $k, $iv, $meta_mac) {
//    $dl_url = api_req(array('a' => 'g', 'g' => 1, 'n' => $file->h));
//    $data_enc = file_get_contents($dl_url->g);
//    $data = aes_ctr_decrypt($data_enc, a32_to_str($k), a32_to_str($iv));
//    file_put_contents($attributes->n, $data);
//    $file_mac = cbc_mac($data, $k, $iv);
//    if (array($file_mac[0] ^ $file_mac[1], $file_mac[2] ^ $file_mac[3]) != $meta_mac) {
//        echo 'MAC mismatch';
//    }
//}
//
//function getfiles() {
//    global $master_key, $root_id, $inbox_id, $trashbin_id;
//
//    $files = api_req(array('a' => 'f', 'c' => 1));
//    foreach ($files->f as $file) {
//        if ($file->t == 0 || $file->t == 1) {
//            $key = substr($file->k, strpos($file->k, ':') + 1);
//            $key = decrypt_key(base64_to_a32($key), Mega::$master_key);
//            if ($file->t == 0) {
//                $k = array($key[0] ^ $key[4], $key[1] ^ $key[5], $key[2] ^ $key[6], $key[3] ^ $key[7]);
//                $iv = array_merge(array_slice($key, 4, 2), array(0, 0));
//                $meta_mac = array_slice($key, 6, 2);
//            } else {
//                $k = $key;
//            }
//            $attributes = base64urldecode($file->a);
//            $attributes = dec_attr($attributes, $k);
////            CVarDumper::dump($file);
//            downloadfile($file, $attributes, $k, $iv, $meta_mac);
//        } else if ($file->t == 2) {
//            Mega::$root_id = $file->k;
//        } else if ($file->t == 3) {
//            Mega::$inbox_id = $file->k;
//        } else if ($file->t == 4) {
//            Mega::$trashbin_id = $file->k;
//        }
//    }
//}

?>