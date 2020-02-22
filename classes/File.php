<?php

/**
 * Created by PhpStorm.
 * User: MoonSeokKang
 * Date: 2/21/20
 * Time: 10:05 PM
 */
class File
{
    public static function process_file($file) {
        $filepath = $_SERVER["DOCUMENT_ROOT"] . "/files/";
        $random_chr = rand(1000, 100000);
        $namearray = explode(".", $file["name"]);
        $ext = array_pop($namearray);
        $new_name = implode(".", $namearray) . "_" . $random_chr . "." . $ext;
        $res = rename($file['tmp_name'], $filepath . $new_name);

        if(!$res) {
            $resultarray["code"]= "400";
            $resultarray["status"] = "400 Bad Request";
            $resultarray["message"] = "Attached file could not be processed. Please contact the support team";
            apiResponse($resultarray, "json", 400, "Bad Request");
        }

        return $filepath . $new_name;

    }

}