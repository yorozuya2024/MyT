<?php

/*
 * BEGIN RSA-related stuff -- taken from PEAR Crypt_RSA package
 * http://pear.php.net/package/Crypt_RSA
 */

function bin2int($str) {
    $result = 0;
    $n = strlen($str);
    do {
        $result = bcadd(bcmul($result, 256), ord($str[--$n]));
    } while ($n > 0);
    return $result;
}

function int2bin($num) {
    $result = '';
    do {
        $result .= chr(bcmod($num, 256));
        $num = bcdiv($num, 256);
    } while (bccomp($num, 0));
    return $result;
}

function bitOr($num1, $num2, $start_pos) {
    $start_byte = intval($start_pos / 8);
    $start_bit = $start_pos % 8;
    $tmp1 = int2bin($num1);

    $num2 = bcmul($num2, 1 << $start_bit);
    $tmp2 = int2bin($num2);
    if ($start_byte < strlen($tmp1)) {
        $tmp2 |= substr($tmp1, $start_byte);
        $tmp1 = substr($tmp1, 0, $start_byte) . $tmp2;
    } else {
        $tmp1 = str_pad($tmp1, $start_byte, '\0') . $tmp2;
    }
    return bin2int($tmp1);
}

function bitLen($num) {
    $tmp = int2bin($num);
    $bit_len = strlen($tmp) * 8;
    $tmp = ord($tmp[strlen($tmp) - 1]);
    if (!$tmp) {
        $bit_len -= 8;
    } else {
        while (!($tmp & 0x80)) {
            $bit_len--;
            $tmp <<= 1;
        }
    }
    return $bit_len;
}

function rsa_decrypt($enc_data, $p, $q, $d) {
    $enc_data = int2bin($enc_data);
    $exp = $d;
    $modulus = bcmul($p, $q);
    $data_len = strlen($enc_data);
    $chunk_len = bitLen($modulus) - 1;
    $block_len = (int) ceil($chunk_len / 8);
    $curr_pos = 0;
    $bit_pos = 0;
    $plain_data = 0;
    while ($curr_pos < $data_len) {
        $tmp = bin2int(substr($enc_data, $curr_pos, $block_len));
        $tmp = bcpowmod($tmp, $exp, $modulus);
        $plain_data = bitOr($plain_data, $tmp, $bit_pos);
        $bit_pos += $chunk_len;
        $curr_pos += $block_len;
    }
    return int2bin($plain_data);
}

/*
 * END RSA-related stuff
 */
?>