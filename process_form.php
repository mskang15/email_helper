<?php
//define("BASE_PATH", dirname(__FILE__));
//define('ROOT_PATH', dirname(__DIR__) . '/');
//$cwd = $_SERVER["DOCUMENT_ROOT"];
//set_include_path("$cwd" . PATH_SEPARATOR . get_include_path());

//set_include_path("$cwd" . PATH_SEPARATOR);

require_once 'autoload.php';
require_once 'functions/functions.php';

$method = $_SERVER['REQUEST_METHOD'];

switch($method){
    case "POST":
        $headers = "From: mskang15@gmail.com" . "\r\n";
//        mail("kangmoonseok@yahoo.com", "test subject", "test message",  $headers);
//        die;
        Email::send("test subject", "sadf", "kangmooonseok@yahoo.com", ["name"=> "test user", "email_address"=>"kangmooonseok@yahoo.com"], ["<div>adsf</div>", "asdfads"]);

            die;
        $email_form = new EmailForm($_POST, $_FILES);
        $error_arr = InputValidation::validate($email_form);
        if(!empty($_FILES["recipients"])) {
            $res_array = Csv::createArrayBasedOnCsv($_FILES["recipients"]["tmp_name"], $error_arr);
            $recipients = $res_array[0];
            $error_arr = $res_array[1];
        }
        
        if(!empty($error_arr)) {
            $error_string = InputValidation::createErrorStringWithArray($error_arr);
            $resultarray["code"]= "400";
            $resultarray["status"] = "400 Bad Request";
            $resultarray["message"] = $error_string;
            apiResponse($resultarray, "json", 400, "Bad Request");
            exit;
        }
        
        $email_form->setField("recipients", $recipients);
        //TODO: process the content, finish email, display the modal report
        //finish email
        $email_result_arr = Email::sendEmail($emailForm);
        $resultarray = Email::generateEmailReport($email_result_arr);
        apiResponse($resultarray, "csv", 200);
        break;
    case "GET":
        //TODO: show how many contacts will be processed
        
        break;
    default:
        $resultarray["code"]= "405";
        $resultarray["status"] = "405 Method Not Allowed";
        $resultarray["message"] = "Allow: POST, GET";
        apiResponse($resultarray, "json", 405, "Method Not Allowed");
        break;
}
