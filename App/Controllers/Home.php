<?php
namespace App\Controllers;

use App\Models\RoomModel;
use App\system\Utils\Utils;
use App\Views\View;

class Home{


    /**
     * Página inicial
     */
    static function index(){
        $view = new View();
        $vars = [];
        $vars['title'] = 'Tela inciail';
        $view->setJsFile('salas.js');
        $view->setJsVar('const', 'listMethod', 'listRoom');
        return $view->render('home',$vars);
    }

    /**
     * Salas
     */
    static function rooms(){
        $view = new View();
        $vars = array();
        $vars['title'] = 'Salas';

        $view->setJsFile('salas.js');
        $view->setJsVar('const', 'listMethod', 'listRoom');
        return $view->render('rooms',$vars);
    }

    /**
     * Agendamento
     */
    static function scheduling(){
        $view = new View();
        $vars = [];
        $vars['title'] = 'Agendamentos';
        $roomModel = new RoomModel();
        $rooms = $roomModel->getAll();

        if(!empty($rooms)){
            $vars['rooms'] = $rooms;
        }

        $view->setJsFile('scheduling.js');
        $view->setJsVar('const', 'listMethod', 'listScheduling');
        return $view->render('scheduling', $vars);
    }

    /**
     * Json swagger
     */
    static function SwaggerJson(){
        $openapi = \OpenApi\Generator::scan([SITE_ROOT."/App/Controllers/Api",SITE_ROOT."/App/Entity"]);
        header('Content-Type: application/json');
        echo $openapi->toJson();
    }

    /**
     * Documentação api
     */
    static function docs(){
        $view = new View();
        $vars = array();
        $vars['title'] = 'Documentação da api';
        return $view->render('docs',$vars);
    }
}