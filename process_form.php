<?php
require_once 'autoload.php';
require_once 'functions/functions.php';

$method = $_SERVER['REQUEST_METHOD'];

switch($method){
    case "POST":

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
        
        var_dump($email_form);
        die;
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
