<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

file_put_contents('./send_mail.log', "Processing started at " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);

// URLパラメータからメールアドレスを取得
if (isset($_GET['email']) && filter_var($_GET['email'], FILTER_VALIDATE_EMAIL)) {
    $recipientEmail = $_GET['email'];
} else {
    echo 'Invalid email address.';
    exit;
}

// URLパラメータから本文を取得
if (isset($_GET['body'])) {
    $body = $_GET['body'];
} else {
    echo 'Invalid email body.';
    exit;
}

mb_language("Japanese");
mb_internal_encoding("UTF-8");

$to = $recipientEmail;
$subject = "Account Confirmation";
$body = $body;
$headers = "From: admin@example.com";

if (mb_send_mail($to, $subject, $body, $headers)) {
    echo "メール送信成功";
} else {
    echo "メール送信失敗";
}

