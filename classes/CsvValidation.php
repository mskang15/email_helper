<?php

/**
 * Created by PhpStorm.
 * User: MoonSeokKang
 * Date: 2/21/20
 * Time: 4:15 PM
 */
class CsvValidation
{
    public static function checkIfFileIsNotEmpty($cell, $error_arr) {
        if(empty($cell)){
            $error_arr[] = "This file is empty";
            return [false, $error_arr];
        }
        return [true, $error_arr];
    }
    
    public static function validateEmailInCsv($email, $error_arr) {
        if(!validateEmail(trim($email))) {
            $error_arr[] = $email . " is not a valid email address";
            return [false, $error_arr];
        }
        return [true, $error_arr];
    }

    public static function checkIfNotEmpty($val, $error_arr) {
        if(!is_array($val)){
            $val = trim($val);
        }

        if(empty($val)) {
            $error_arr[] = $val . " cannot be empty";
            return [false, $error_arr];
        }
        return [true, $error_arr];
    }

    public static function checkForRecipient($array, $error_arr) {
        if(empty($array)){
            $error_arr[] = "No recipient found";
            return [false, $error_arr];
        }
        return [true, $error_arr];
    }
    
    public static function checkForRequiredHeaders($headings, $error_arr) {
        $required_headers = ["name", "email_address"];

        //refactored $headings for validation purposes
        $new_headings = [];
        foreach($headings as $heading){
            $new_headings[$heading] = $heading;
        }

        foreach($required_headers as $rh){
            if(empty($new_headings[$rh])){
                $error_arr[] = "$rh is a required csv header";
            }
        }

        return $error_arr;
    }

}