<?php

function base64urldecode($data) {
    $data .= substr('==', (2 - strlen($data) * 3) % 4);
    $data = str_replace(array('-', '_', ','), array('+', '/', ''), $data);
    return base64_decode($data);
}

function base64urlencode($data) {
    return str_replace(array('+', '/', '='), array('-', '_', ''), base64_encode($data));
}

function a32_to_str($hex) {
    return call_user_func_array('pack', array_merge(array('N*'), $hex));
}

function a32_to_base64($a) {
    return base64urlencode(a32_to_str($a));
}

function str_to_a32($b) {
    // Add padding, we need a string with a length multiple of 4
    $b = str_pad($b, 4 * ceil(strlen($b) / 4), "\0");
    return array_values(unpack('N*', $b));
}

function base64_to_a32($s) {
    return str_to_a32(base64urldecode($s));
}

function aes_cbc_encrypt($data, $key) {
    return mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $key, $data, MCRYPT_MODE_CBC, "\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0");
}

function aes_cbc_decrypt($data, $key) {
    return mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $key, $data, MCRYPT_MODE_CBC, "\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0");
}

function aes_cbc_encrypt_a32($data, $key) {
    return str_to_a32(aes_cbc_encrypt(a32_to_str($data), a32_to_str($key)));
}

function aes_cbc_decrypt_a32($data, $key) {
    return str_to_a32(aes_cbc_decrypt(a32_to_str($data), a32_to_str($key)));
}

function aes_ctr_encrypt($data, $key, $iv) {
    return mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $key, $data, 'ctr', $iv);
}

function aes_ctr_decrypt($data, $key, $iv) {
    return mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $key, $data, 'ctr', $iv);
}

//include Yii::app()->basePath . '/views/site/pages/_rsa.php';

function stringhash($s, $aeskey) {
    $s32 = str_to_a32($s);
    $h32 = array(0, 0, 0, 0);

    for ($i = 0; $i < count($s32); $i++) {
        $h32[$i % 4] ^= $s32[$i];
    }

    for ($i = 0; $i < 0x4000; $i++) {
        $h32 = aes_cbc_encrypt_a32($h32, $aeskey);
    }

    return a32_to_base64(array($h32[0], $h32[2]));
}

function prepare_key($a) {
    $pkey = array(0x93C467E3, 0x7DB0C7A4, 0xD1BE3F81, 0x0152CB56);

    for ($r = 0; $r < 0x10000; $r++) {
        for ($j = 0; $j < count($a); $j += 4) {
            $key = array(0, 0, 0, 0);

            for ($i = 0; $i < 4; $i++) {
                if ($i + $j < count($a)) {
                    $key[$i] = $a[$i + $j];
                }
            }

            $pkey = aes_cbc_encrypt_a32($pkey, $key);
        }
    }

    return $pkey;
}

function encrypt_key($a, $key) {
    $x = array();

    for ($i = 0; $i < count($a); $i += 4) {
        $x = array_merge($x, aes_cbc_encrypt_a32(array_slice($a, $i, 4), $key));
    }

    return $x;
}

function decrypt_key($a, $key) {
    $x = array();

    for ($i = 0; $i < count($a); $i += 4) {
        $x = array_merge($x, aes_cbc_decrypt_a32(array_slice($a, $i, 4), $key));
    }

    return $x;
}

function mpi2bc($s) {
    $s = bin2hex(substr($s, 2));
    $len = strlen($s);
    $n = 0;
    for ($i = 0; $i < $len; $i++) {
        $n = bcadd($n, bcmul(hexdec($s[$i]), bcpow(16, $len - $i - 1)));
    }
    return $n;
}

function get_chunks($size) {
    $chunks = array();
    $p = $pp = 0;

    for ($i = 1; $i <= 8 && $p < $size - $i * 0x20000; $i++) {
        $chunks[$p] = $i * 0x20000;
        $pp = $p;
        $p += $chunks[$p];
    }

    while ($p < $size) {
        $chunks[$p] = 0x100000;
        $pp = $p;
        $p += $chunks[$p];
    }

    $chunks[$pp] = ($size - $pp);
    if (!$chunks[$pp]) {
        unset($chunks[$pp]);
    }

    return $chunks;
}

function cbc_mac($data, $k, $n) {
    $padding_size = (strlen($data) % 16) == 0 ? 0 : 16 - strlen($data) % 16;
    $data .= str_repeat("\0", $padding_size);

    $chunks = get_chunks(strlen($data));
    $file_mac = array(0, 0, 0, 0);

    foreach ($chunks as $pos => $size) {
        $chunk_mac = array($n[0], $n[1], $n[0], $n[1]);
        for ($i = $pos; $i < $pos + $size; $i += 16) {
            $block = str_to_a32(substr($data, $i, 16));
            $chunk_mac = array($chunk_mac[0] ^ $block[0], $chunk_mac[1] ^ $block[1], $chunk_mac[2] ^ $block[2], $chunk_mac[3] ^ $block[3]);
            $chunk_mac = aes_cbc_encrypt_a32($chunk_mac, $k);
        }
        $file_mac = array($file_mac[0] ^ $chunk_mac[0], $file_mac[1] ^ $chunk_mac[1], $file_mac[2] ^ $chunk_mac[2], $file_mac[3] ^ $chunk_mac[3]);
        $file_mac = aes_cbc_encrypt_a32($file_mac, $k);
    }

    return $file_mac;
}
?>