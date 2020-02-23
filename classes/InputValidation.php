<?php

/**
 * Created by PhpStorm.
 * User: MoonSeokKang
 * Date: 2/21/20
 * Time: 1:13 AM
 */
class InputValidation
{
    public static function validate(EmailForm $email_form) {
        $error_arr = [];
        $error_arr = self::checkRequiredFields($email_form, $error_arr);
        $error_arr = self::checkEmail($email_form, $error_arr);
        return $error_arr;
    }
    
    private static function checkRequiredFields(EmailForm $email_form, $error_arr) {
        $required_fields = ["subject", "content", "recipients", "from_email", "from"];
        foreach($required_fields as $field){
            $field_obj = $email_form->getField($field);
            if($field_obj["type"] === "file" || $field_obj["type"] === "csv" || $field === "content") {
                if (empty($field_obj["val"])) {
                    $error_arr[] = $field . " is missing";
                }
            } else {
                if(trim($field_obj["val"]) == ""){
                    $error_arr[] = $field . " is missing";
                }
            }
        }
        return $error_arr;
    }
    
    private static function checkEmail(EmailForm $email_form, $error_arr) {
        $email = $email_form->getField("from_email")["val"];
        if(!validateEmail($email)){
            $error_arr[] = "from_email is invalid";
        }
        return $error_arr;
    }

    public static function createErrorStringWithArray($error_arr) {
        $error_string = "";
        if(count($error_arr) > 0) {
            foreach($error_arr as $err) {
                $error_string .= $err . ",";
            }
            $error_string = rtrim($error_string, ",");
        }
        return $error_string;
    }
}