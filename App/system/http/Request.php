<?php
namespace  App\system\http;
use const App\Config\DEFAULT_ACTION;
use const App\Config\DEFAULT_CONTROLLER;

/**
 * Classe responsável pelo gerenciamento das requisições
 * @author Renan Salustiano <renansalustiano2020@gmail.com>
 */
class Request{

    private array $getParams;
    private array $postParams;
    private array $headers;
    private string $requestedUri;
    private string $controller;
    private string $action;
    private array $params;
    private string $requestedMethod;
    private array $jsonParams;


    public function __construct()
    {
        $this->requestedUri = $this->getUri();

        $this->getParams = $_GET ?? [];
        $this->postParams = $_POST ?? [];
        $this->requestedMethod = $_SERVER['REQUEST_METHOD'] ?? null;
        $this->headers = getallheaders();
        $this->jsonParams = json_decode(file_get_contents("php://input"), true) ?? [];

    }

    /**
     * Método responsável por retornar a url da requisição
     * @return string
     */
    private function getUri() : string {
        $uri = "/";
        if(isset($_GET['uri'])){
            $uri = filter_input(INPUT_GET, 'uri', FILTER_DEFAULT);
            unset($_GET['uri']);
        }
        return $uri;
    }

    /**
     * Método responsável por retornar todos ou 1 parâmetros especifico do tipo GET
     * @param string $paramName chave do parâmetro GET especifico
     * @return mixed|null
     */
    public function getGetParams(string $paramName = null){
        if(isset($this->getParams[$paramName]) && !empty($this->getParams[$paramName]))
            return $this->getParams[$paramName];
        return null;
        return $this->getParams[$paramName] ?? $this->getParams;
    }

    /**
     * Método responsável por retornar todos ou 1 parâmetros especifico do tipo POST
     * @param string $paramName chave do parâmetro POST especifico
     * @return mixed|null
     */
    public function getPostParams(string $paramName = null){
        if(!empty($paramName)){
            if(isset($this->postParams[$paramName]) && !empty($this->postParams[$paramName]))
                return $this->postParams[$paramName];
            return null;
        }
        return $this->postParams[$paramName] ?? $this->postParams;
    }


    public function getRequestedUri() : string{
        return $this->requestedUri;
    }

    public function getRequestedMethod(){
        return $this->requestedMethod;
    }

    /**
     * @param string|null $paramName
     * @return array|mixed|null
     */
    public function getJsonParams(string $paramName = null){
        if(!empty($paramName)){
            if(isset($this->jsonParams[$paramName]) && !empty($this->jsonParams[$paramName]))
                return $this->jsonParams[$paramName];
            return null;
        }
        return $this->jsonParams[$paramName] ?? $this->jsonParams;
    }
}