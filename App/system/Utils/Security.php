<?php
namespace App\system\Utils;

class Security{

    static function securityString( $string){
        $string = htmlentities($string);
        $string = htmlspecialchars($string);
        $string = filter_var($string, FILTER_SANITIZE_STRING);
        return html_entity_decode($string);
    }
    static function validateInt($arg){
        return filter_var($arg, FILTER_VALIDATE_INT);
    }
}