<?php
/**
 * Created by PhpStorm.
 * User: MoonSeokKang
 * Date: 2/21/20
 * Time: 10:08 PM
 */

class Quill {
    public static function generateHtmlWithText($text){
        $text_arr = json_decode($text, true);

        var_dump($text_arr);
        $html_builder = "";
        $text_builder = "";
        foreach($text_arr["ops"] as $txt_a) {
            if(empty(trim($txt_a["insert"]))){
                continue;
            }
            $html = "";
            if(!empty($txt_a["attributes"])){
                $html = $txt_a["insert"];
                foreach($txt_a["attributes"] as $attribute => $attribute_val){
                    switch($attribute){
                        case "bold":
                            $html = "<strong>" . $html . "</strong>";
                            break;
                        case "italic":
                            $html = "<i>" . $html . "</i>";
                            break;
                        case "underline":
                            $html = "<u>" . $html . "</u>";
                            break;
                        case "link":
                            $html = "<a href='{$attribute_val}'>" . $html . "</a>";
                            break;
                    }
                }
                $html .= "<br>";
            } else {
                $html_builder = preg_replace("/<br>$/", "", $html_builder);
                $text_builder = preg_replace("/\n$/", "", $text_builder);
                $string_arr = explode("\n", $txt_a["insert"]);
                foreach($string_arr as $string) {
                    $html .= $string . "<br>";
                }
            }
            $text_builder .= $txt_a["insert"] . "\n";
            $html_builder .= $html;
        }
        $html_builder = preg_replace("/<br><br>$/", "", $html_builder);
        $text_builder = preg_replace("/\n$/", "", $text_builder);
        var_dump($html_builder);
        die;

        return [$html_builder, $text_builder];
    }
}