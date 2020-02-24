<?php

/**
 * Created by PhpStorm.
 * User: MoonSeokKang
 * Date: 2/21/20
 * Time: 12:52 AM
 */
class CsvException extends Exception {}

class Csv
{
    public static function createArrayBasedOnCsv($file, $error_arr=[]) {
        $array = [];
        $i = 0;
        $headings = [];
        if (($handle = fopen($file, "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 10000, ",")) !== FALSE) {
                if($i === 0){
                    foreach($data as $d){
                        $res = CsvValidation::checkIfFileIsNotEmpty($d, $error_arr);
                        if($res[0] === false){
                            $error_arr = $res[1];
                            return [[], $error_arr];
                        }
                        $headings[] = strtolower(trim($d));
                    }
                    $error_arr  = CsvValidation::checkForRequiredHeaders($headings, $error_arr);
                    $i++;
                    continue;
                }

                for($j=0;$j < count($data); $j++) {
                    $array[$i - 1][$headings[$j]] = trim($data[$j]);
                    if(preg_match("/email/",$headings[$j])){
                        $res = CsvValidation::validateEmailInCsv($data[$j], $error_arr);
                        if($res[0] === false){
                            $error_arr = $res[1];
                            return [[], $error_arr];
                        }
                    }
                    if($headings[$j] === "name"){
                        $res = CsvValidation::checkIfNotEmpty($data[$j], $error_arr);
                        if($res[0] === false){
                            $error_arr = $res[1];
                            return [[], $error_arr];
                        }
                    }
                }
                $i++;
            }
            fclose($handle);
        }

        $res = CsvValidation::checkForRecipient($array, $error_arr);
        if($res[0] === false){
            $error_arr = $res[1];
        }

        return [$array, $error_arr];
    }
    
}