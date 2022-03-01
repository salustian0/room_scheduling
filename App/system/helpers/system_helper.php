<?php
namespace App\system\helpers;

/**
 * Função para debug de variáveis
 * @param $arg
 * @param false $die
 */
function debug($arg, bool $die = false){
    echo '<pre>';
    print_r($arg);
    echo '</pre>';
    if($die){
        die();
    }
}