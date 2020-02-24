<?php

/**
 * Created by PhpStorm.
 * User: MoonSeokKang
 * Date: 2/21/20
 * Time: 1:58 PM
 */

require_once __DIR__ . '/../lib/Phpmailer/src/PHPMailer.php';
require_once __DIR__ . '/../lib/Phpmailer/src/SMTP.php';
require_once __DIR__ . '/../lib/Phpmailer/src/Exception.php';

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
        $is_html = true;
        //attachment
        $attachments = $emailForm->getField("attachment")["val"];
        $body = $emailForm->getField("content")["val"];

        //for the report
        $email_result_arr = [];

        foreach($recipients as $recipient){
            // populate {user_defined_field}
            $updated_body = self::prepareEmailTemplate($body, $recipient);
            if(self::send($subject, $from, $from_email, $recipient, $updated_body, $is_html, $attachments)){
                $email_result_arr[] = [
                    "name" => $recipient["name"],
                    "email_address" => $recipient["email_address"],
                    "result" => "sent"
                ];
            } else {
                $email_result_arr[] = [
                    "name" => $recipient["name"],
                    "email_address" => $recipient["email_address"],
                    "result" => "sent"
                ];
            }
        }
        return $email_result_arr;
    }

    private static function prepareEmailTemplate($body, $recipient) {
        if(is_array($body)){
            $html_body = $body[0];
            $text_body = $body[1];

            foreach($recipient as $key => $val) {
                $html_body = preg_replace("/\{".preg_quote($key)."\}/", "$val", $html_body);
                $text_body = preg_replace("/\{".preg_quote($key)."\}/", "$val", $text_body);
            }
            return [$html_body, $text_body];
        } else {
            foreach($recipient as $key => $val) {
                $body = preg_replace("/\{".preg_quote($key)."\}/", "$val", $body);
            }
            return $body;
        }
    }
    
    public static function send($subject, $from, $from_email, $recipient, $body, $is_html = false, $attachments = null) {

        $mail = new PHPMailer(true);

        try {
//            $mail->setFrom('mskang15@gmail.com', 'Moon Test');
            $mail->setFrom($from_email, $from);
            $mail->SMTPAuth = false;
            $mail->SMTPAutoTLS = false;
            $mail->addAddress($recipient["email_address"], $recipient["name"]);
            $mail->Subject = $subject;

            //for testing email delveriability
//            $is_html = false;

            if($is_html){
                $mail->isHTML(true);
            }

            if(is_array($body)){
                if(!$is_html){
                    $mail->Body = $body[1]; // text body
                } else {
                    $mail->Body = $body[0]; // html body
                    $mail->AltBody = $body[1]; // text body
                }
            } else {
                $mail->Body = $body; // only text body
            }

            if(!empty($attachments)) {
                if(is_array($attachments)){
                    foreach($attachments as $attachment){
                        $mail->addAttachment($attachment);
                    }
                } else {
                    $mail->addAttachment($attachments);
                }
            }

            error_log(print_r($mail, true));

            if(!$res = $mail->send()){
                error_log("not sent!");
                // throw new Exception("something went wrong with sending the email");
                return false;
            }
            error_log($res);
            error_log("sent!");
            return true;

        } catch (Exception $e) {
            error_log("Message could not be sent. Mailer Error: {$mail->ErrorInfo}");
        }
    }

    public static function generateResultArrayForEmailReport($report_array) {
        $resultarray = [];

        for($i=0; $i<count($report_array); $i++) {
            $resultarray["info"][$i] = $report_array[$i];
        }

        return $resultarray;
    }


}