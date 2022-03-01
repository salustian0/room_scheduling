<?php
namespace App\system\Utils;
class Utils{
    static function formatDate($date, $format = 'd/m/Y'){
        return date($format,strtotime($date));
    }
}