<?php
namespace App\Controllers;
use App\Views\View;

class BaseController{
    /**
     * @param $code
     * @param null $message
     */
    static function error($code, $message = null){
        $view = new View();
        $vars = array();

        $vars['title'] = "Erro ao acessar a página";
        $vars['code'] = $code;
        $vars['message'] = $message;

        $view->render('system/erro',$vars);
    }
}