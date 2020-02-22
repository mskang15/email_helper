<?php
/**
 * Created by PhpStorm.
 * User: MoonSeokKang
 * Date: 2/21/20
 * Time: 1:36 AM
 */

spl_autoload_register('classAutoload', true);
spl_autoload_register('modelAutoload', true);

function classAutoload($class_name) {
    $rootpath = dirname(__FILE__);
    $classes_folder_path = $rootpath . DIRECTORY_SEPARATOR . "classes" . DIRECTORY_SEPARATOR;
    $filename = $class_name . ".php";
    $file = $classes_folder_path.$filename;

    if(!file_exists($file)){
        return false;
    }
    include $file;
}

function modelAutoload($class_name) {
    $rootpath = dirname(__FILE__);
    $models_folder_path = $rootpath . DIRECTORY_SEPARATOR . "models" . DIRECTORY_SEPARATOR;
    $filename = $class_name . ".php";
    $file = $models_folder_path.$filename;

    if(!file_exists($file)){
        return false;
    }
    include $file;
}
