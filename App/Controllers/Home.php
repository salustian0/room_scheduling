<?php
namespace App\Controllers;

use App\Models\RoomModel;
use App\system\Utils\Utils;
use App\Views\View;

class Home{


    static function index(){
        $view = new View();
        $view->setJsFile('salas.js');
        $view->setJsVar('const', 'listMethod', 'listRoom');
        return $view->render('home');
    }


    static function rooms(){
        $view = new View();
        $view->setJsFile('salas.js');
        $view->setJsVar('const', 'listMethod', 'listRoom');
        return $view->render('rooms');
    }

    static function scheduling(){
        $view = new View();
        $vars = [];

        $roomModel = new RoomModel();
        $rooms = $roomModel->getAll();

        if(!empty($rooms)){
            $vars['rooms'] = $rooms;
        }

        $view->setJsFile('scheduling.js');
        $view->setJsVar('const', 'listMethod', 'listScheduling');
        return $view->render('scheduling', $vars);
    }
}