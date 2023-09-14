<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'mailer/src/Exception.php';
require 'mailer/src/PHPMailer.php';
Class Mail {
    static function sendMail($receiver, $subject, $html_body, $non_html_body = 'This message is only avilable in HTML, use a different browser!', $sender = 'no-reply@bytelover.com', $sender_name = 'ByteLover'){
        $mail = new PHPMailer(true);
    try {
        $mail->Host = 'mail.bytelover.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'no-reply@bytelover.com';
        $mail->Password = '?Hz,UW!q6*PU';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port = 465;
        $mail->setFrom($sender, $sender_name);
        $mail->addAddress($receiver);
        $mail->CharSet = 'UTF-8';
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $html_body;
        $mail->AltBody = $non_html_body;
        $mail->send();
        return true;
    } catch (Exception $e) {
        return $e;
    }
    }
}