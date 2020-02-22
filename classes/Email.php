<?php

/**
 * Created by PhpStorm.
 * User: MoonSeokKang
 * Date: 2/21/20
 * Time: 1:58 PM
 */
require_once '../lib/Phpmailer/src/PHPMailer.php';
require_once '../lib/Phpmailer/src/SMTP.php';
require_once '../lib/Phpmailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class Email
{
    public static function sendEmail(EmailForm $emailForm)
    {
        $recipients = $emailForm->getField("recipients")["val"];
        $subject = $emailForm->getField("subject")["val"];
        $from = $emailForm->getField("from")["val"];
        $from_email = $emailForm->getField("from_email")["val"];
        //attachment
        $body = $emailForm->getField("content");

        foreach($recipients as $recipient){
            self::send($subject, $from, $from_email, $recipient, $body);
        }
    }

    public static function prepareEmailBody($body, $recipient) {

    }
    
    private static function send($subject, $from, $from_email, $recipient, $body) {
        $mail = new PHPMailer(true);
        try {
            $mail->setFrom('mskang15@gmail.com', 'Moon Test');
//            $mail->setFrom($from_email, $from);
            $mail->addAddress($recipient["email_address"], $recipient["name"]);
            $mail->isHTML(false);
            $mail->Subject = $subject;
            $mail->Body = $body;
            $mail->send();

        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    }


}