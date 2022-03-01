<?php
namespace App\system\helpers;

class Load{
    
    static function controller($controller){
        $controller = "\\App\\Controllers\\{$controller}";
        if(class_exists($controller)){
            echo "ok";
        }
    }

    static function helper($helper){
        $path = "\\App\\system\\helpers\\{$helper}.php";
        if(file_exists($path)){
            echo 'ok';
        }
    }
}