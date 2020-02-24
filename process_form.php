<?php

require_once 'autoload.php';
require_once 'defaults.api.php';
require_once 'functions/functions.php';

//list($me, $myid, $other) = preg_split("/\//", $action, 1);
switch($method){
    case "POST":
        if(isset($action) && $action === "count-recipients"){
            $csv_array = Csv::createArrayBasedOnCsv($_FILES["recipients"]["tmp_name"]);
            if(!empty($csv_array[1])) {
                $error_string = InputValidation::createErrorStringWithArray($csv_array[1]);
                $resultarray["code"]= "400";
                $resultarray["status"] = "400 Bad Request";
                $resultarray["message"] = $error_string;
                apiResponse($resultarray, "json", 400, "Bad Request");
                exit;
            }
            apiResponse(count($csv_array[0]), "json");
            exit;
        }

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
        //TODO: display the modal report
        //finish email
        $email_result_arr = Email::sendEmail($email_form);
        $resultarray = Email::generateResultArrayForEmailReport($email_result_arr);
         apiResponse($resultarray, "json", 200);
        exit;
        break;
    case "GET":
        break;
    default:
        $resultarray["code"]= "405";
        $resultarray["status"] = "405 Method Not Allowed";
        $resultarray["message"] = "Allow: POST, GET";
        apiResponse($resultarray, "json", 405, "Method Not Allowed");
        break;
}
