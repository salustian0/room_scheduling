<?php
namespace  App\Config;
use \App\Controllers as Ctrl;
use \App\Controllers\Api as Api;

use App\system\http\Request;
use App\system\http\Router;
if(!(isset($router) && $router instanceof Router) ) die("Houve um erro na tentativa de registro das rotas");

/**
 * Rota padrão
 */
$router->addGetRoute('/|home', function(){
    return Ctrl\Home::index();
});

$router->addGetRoute('salas', function(){
    return Ctrl\Home::rooms();
});

$router->addGetRoute('agendamento', function(){
    return Ctrl\Home::scheduling();
});


/* ROTAS DA API */
/**
 * Rota show
 */
$router->addGetRoute('api/salas/show', function(Request $request){
    return Api\Room::show($request);
});
/**
 * Rota show por id
 */
$router->addGetRoute('api/salas/show/{idSala}', function(Request $request, int $idSala){
    return Api\Room::show($request,$idSala);
});
/**
 * Rota create
 */
$router->addPostRoute('api/salas/registrar', function(Request $request){
    return Api\Room::create($request);
});

/* ROTAS AGENDAMENTO */
$router->addPostRoute('api/agendamento/registrar', function (Request $request){
    return Api\RoomScheduling::create($request);
});

$router->addGetRoute('api/agendamento/show', function (Request $request){
    return Api\RoomScheduling::show($request);
});

$router->addDeleteRoute('api/salas/delete/{idRoom}', function (int $idRoom){
    return Api\Room::delete($idRoom);
});

$router->addDeleteRoute('api/agendamentos/delete/{idAgendamento}', function (int $idAgendamento){
    return Api\RoomScheduling::delete($idAgendamento);
});

$router->addGetRoute('swagger/json', function (){
    return Ctrl\Home::SwaggerJson();
});

$router->addGetRoute('docs', function (){
    return Ctrl\Home::docs();
});