<?php
date_default_timezone_set('America/Sao_Paulo');

/**
 * Start session
 */
if(!isset($_SESSION)){
    session_start();
}
/**
 * Ponto inicial do projeto, index.php responsável por instanciar e chamar
 * tudo o que será necessário para o funcionamento correto desse mini framework.
 *
 * Totalmente pensado e desenvolvido por mim Renan Salustiano
 * tendo como base minha experiência com frameworks mvc como Codeigniter, Laravel ...
 *
 * @author Renan Salustiano <renan.alustiano2020@gmail.com>
 */
require_once "App/Config/config.php";


/**
 * Ativação dos erros
 */
if(MAINTENANCE == true){
    die("Estamos em manutenção, por favor tente novamente mais tarde");
}

/**
 * Ativação dos erros
 */
if(ENVIRONMENT == "development"){
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
}






/**
 * Autoload psr-4
 */
require_once "vendor/autoload.php";

/**
 * Configurações necessárias
 */

/**
 * Constants que servirão para todo o projeto
 */
require_once "App/Config/constants.php";

/**
 * Helpers do sistema
 */
require_once "App/system/helpers/system_helper.php";

/**
 * Instancia global da classe request
 */
$request = new \App\system\http\Request();
/**
 * Instancia da classe de roteamento
 */
$router = new \App\system\http\Router($request);
/**
 * Inclusão do arquivo onde o usuário do framework irá registrar as rotas
 */
require_once "App/Config/routes.php";
$router->run();

