<?php

require_once 'autoload.php';
require_once 'functions/functions.php';

$method = $_SERVER['REQUEST_METHOD'];

// list($segment1, $segment2, $segment3) = preg_split("/\//", $_SERVER["REQUEST_URI"], 3);

switch($method){
    case "POST":
        if($_POST["recipient_count"]){
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
        // apiResponse($resultarray, "csv", 200);
        break;
    case "GET":
        //TODO: show how many contacts will be processed
        // mail("kangmoonseok@yahoo.com", "Test email", "test");
        // mail("mskang15@gmail.com", "Test email2", "test2");
        // Email::send("test", "Moon", "mskang15@proveahnsahnghong.com", ["name"=>"moon2","email_address" =>"kangmoonseok@yahoo.com"], "somebody", false);
        // Email::send("test2", "Moon2", "mskang15@proveahnsahnghong.com", ["name"=>"moon1","email_address" =>"mskang15@gmail.com"], "asasdfdasf", false);
        var_dump($_SERVER["REQUEST_URI"]);
        break;
    default:
        $resultarray["code"]= "405";
        $resultarray["status"] = "405 Method Not Allowed";
        $resultarray["message"] = "Allow: POST, GET";
        apiResponse($resultarray, "json", 405, "Method Not Allowed");
        break;
}
