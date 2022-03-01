<?php
namespace  App\Config;
use \App\Controllers as Ctrl;
use App\system\http\Request;
use App\system\http\Router;
if(!(isset($router) && $router instanceof Router) ) die("Houve um erro na tentativa de registro das rotas");

/**
 * Rota padrão
 */
$router->addGetRoute('/', function(){
    echo "welcome";
});
