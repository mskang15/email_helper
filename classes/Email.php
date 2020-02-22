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

    public $failed_emails = [];

    public static function sendEmail(EmailForm $emailForm)
    {
        $recipients = $emailForm->getField("recipients")["val"];
        $subject = $emailForm->getField("subject")["val"];
        $from = $emailForm->getField("from")["val"];
        $from_email = $emailForm->getField("from_email")["val"];
        //attachment
        $body = $emailForm->getField("content")["val"];

        //for the report
        $success = 0;
        $failure = 0;

        foreach($recipients as $recipient){
            if(self::send($subject, $from, $from_email, $recipient, $body)){
                $success++;
            } else {
                $failure++;
            }
        }
        return [$success, $failure, $failed_emails];
    }
    
    private static function send($subject, $from, $from_email, $recipient, $body) {
        $html_body = $body[0];
        $text_body = $body[1];

        $mail = new PHPMailer(true);
        try {
            $mail->setFrom('mskang15@gmail.com', 'Moon Test');
//            $mail->setFrom($from_email, $from);
            $mail->addAddress($recipient["email_address"], $recipient["name"]);
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $html_body;
            $mail->AltBody = $text_body;
            if(!$mail->send()){
                // throw new Exception("something went wrong with sending the email");
                $this->$failed_emails[] = $recipient["email_address"];
                return false;
            }
            return true;

        } catch (Exception $e) {
            error_log("Message could not be sent. Mailer Error: {$mail->ErrorInfo}");
        }
    }

    public static function createEmailReport($report_array) {

    }


}