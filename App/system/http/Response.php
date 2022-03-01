<?php
namespace App\system\http;
use MongoDB\Driver\Session;

/**
 * Classe responsável pelo gerenciamento das respostas do servidor
 * @author Renan Salustiano <renansalustiano2020@gmail.com>
 */
class Response{
    /**
     * @param int $code;
     */
    private int $code = 200;
    /**
     * Conteúdo da resposta
     * @param mixed $content
     */
    private $content;

    /**
     * Cabeçalhos de resposta
     * @param array $headers
     */
    private array $headers;
    /**
     * @param string Tipo do conteúdo da resposta
     */
    private string $contentType = 'text/html';

    public function __construct()
    {
        $this->headers = array();
    }


    /**
     * Código http da resposta
     * @return int
     */
    public function getCode(): int
    {
        return $this->code;
    }

    /**
     * Código http da resposta
     * @param int $code
     */
    public function setCode(int $code): Response
    {
        $this->code = $code;
        return $this;
    }


    /**
     * Conteúdo da resposta
     * @param mixed $content
     */
    public function setContent($content): Response
    {
        $this->content = $content;
        return $this;
    }


    /**
     * Header
     * @param $key
     * @param $value
     */
    public function setHeader($key, $value): Response
    {
        $this->headers[$key] = $value;
        return $this;
    }


    /**
     * @param string $contentType
     */
    public function setContentType(string $contentType): Response
    {
        $this->contentType = $contentType;
        $this->setHeader("Content-Type", "application/json");
        return $this;
    }

    /**
     * Envia os cabecalhos
     */
    private function sendHeaders() : void{
        http_response_code($this->code);
        foreach ($this->headers as $key => $value){
            header($key . ':' . $value);
        }
    }

    /**
     * Envia a resposta
     */
    public function sendResponse(){
        $this->sendHeaders();
        switch ($this->contentType){
            case 'text/html':
                echo $this->content;
                exit;
            case 'application/json':
                $this->content = (array)$this->content;
                echo json_encode($this->content, JSON_UNESCAPED_UNICODE);
                exit;
        }
    }

    public function redirect($route,$message = [], $oldData = []){
        if(!empty($message))
            \App\system\Utils\Session::setFlashData("message", $message);
        if(!empty($oldData))
            \App\system\Utils\Session::setFlashData("oldData", $oldData);

        header('Location: '.SITE_URL.$route);
    }

}