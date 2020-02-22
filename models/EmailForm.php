<?php

/**
 * Created by PhpStorm.
 * User: MoonSeokKang
 * Date: 2/21/20
 * Time: 1:35 AM
 */
class EmailForm
{
    private $subject = [
        "type"=> "string",
        "val" => ""
    ];

    private $from_email = [
        "type"=> "email",
        "val" => ""
    ];

    private $from = [
        "type"=> "string",
        "val" => ""
    ];

    private $recipients = [
        "type" => "csv",
        "val" => ""
    ];

    private $content = [
        "type" => "string",
        "val" => ""
    ];

    private $attachment = [
        "type" => "file",
        "val" => ""
    ];

    public function __construct($post, $files)
    {
        foreach($post as $key => $val) {
            if($key === "content"){
                $val = Quill::generateHtmlWithText($val);
                $this->$key["val"] = trim($val);
            } else {
                $this->$key["val"] = trim($val);
            }
        }

        foreach($files as $key => $val) {
            if($this->$key["type"] === "file"){
                $filepathname = File::process_file($val);
                $this->$key["val"] = $filepathname;
            } else {
                $this->$key["val"] = $val;
            }
        }
    }
    
    public function getField($key) {
        return $this->$key;
    }
    
    public function setField($key, $val) {
        $this->$key["val"] = $val;
    }
}