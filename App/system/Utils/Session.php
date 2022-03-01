<?php
namespace App\system\Utils;

class Session{

    static function setFlashData($name, $value){
        if(!empty($name) && !empty($value)) $_SESSION['flash_data'][$name] = $value;
    }

    static function getFlashData($name){
        $flashData = $_SESSION['flash_data'][$name] ?? [];
        if(!empty($flashData)){
            unset($_SESSION['flash_data'][$name]);
        }
        return $flashData;
    }
    static function getAllFlashData(){
        $flashData = $_SESSION['flash_data'] ?? [];
        unset($_SESSION['flash_data']);
        return $flashData;
    }

    /**
     * @param $sessionName
     * @param $sessionValue
     */
    static function setSession($sessionName, $sessionValue){
        $_SESSION[$sessionName] = $sessionValue;
    }

    /**
     * Validação de sessão de login
     * @return bool
     */
    static function verifySession(){
        if(isset($_SESSION['_USER']) && !empty($_SESSION['_USER'])){
            return true;
        }
        return false;
    }

    /**
     * Obtem dados de uma sessão a partir do indice
     * @param $sessionName
     * @return mixed|null
     */
    static function getSession($sessionName){
        return isset($_SESSION[$sessionName]) ? $_SESSION[$sessionName] : null;
    }

}