<?php

namespace App\system\http;
use App\Controllers\BaseController;
use \Closure;
use MongoDB\Driver\Exception\ServerException;
use ReflectionFunction;
use \Exception;

/**
 * Classe responsável por registrar/buscar e chamar  a rota informada na url
 * @author Renan Salustiano <renan.salustiano2020@gmail.com>
 */
class Router
{
    private array $routes;
    private Request $request;


    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Adiciona uma rota do tipo GET
     * @param $path
     * @param Closure $closure
     * @param array $params
     */
    public function addGetRoute($path, Closure $closure)
    {
        self::registerRoute('GET', $path, $closure);
    }

    public function addDeleteRoute($path, Closure $closure)
    {
        self::registerRoute('DELETE', $path, $closure);
    }

    public function addPutRoute($path, Closure $closure)
    {
        self::registerRoute('PUT', $path, $closure);
    }

    /**
     * Adiciona uma rota do tipo POST
     * @param $path
     * @param Closure $closure
     * @param array $params
     */
    public function addPostRoute($path, Closure $closure)
    {
        self::registerRoute('POST', $path, $closure);
    }

    /**
     * Converte o path em regex e registra a rota
     * @param $type
     * @param $path
     * @param Closure $closure
     */
    private function registerRoute($type, $path, Closure $closure)
    {
        $path = '/^' . str_replace('/', '\/', $path) . '\/?$/';
        $patternParams = '/{(.+?)}/';
        $requiredParams = [];
        if (preg_match_all($patternParams, $path, $matches)) {
            $requiredParams = $matches[1];
            $path = str_replace($matches[0], '([a-zA-Z0-9_-]+)', $path);
        }

        $this->routes[$path] = array(
            'method' => $type,
            'closure' => $closure,
            'params' => $requiredParams,
        );

    }


    /**
     * @return \Exception|mixed|void
     */
    private function getRoute() : array
    {
        $uri = $this->request->getRequestedUri();
        foreach ($this->routes as $path => $route) {
            if (preg_match($path, $uri, $matches)) {

                if ($route['method'] !== $this->request->getRequestedMethod()) {
                    throw new Exception("Método inválido", 405);
                }

                $keys = array_values($route['params']);
                unset($matches[0]);

                $route['params'] = array_filter(array_combine($keys, $matches));
                $route['type'] = 'route';
                return $route;
            }
        }
        return [];
    }

    public function run()
    {
        $patternApi= '/^api\//';

        try{
            $route = $this->getRoute();
            if(!empty($route)){
                $reflection = new ReflectionFunction($route['closure']);
                $arrParams = [];
                foreach ($reflection->getParameters() as $parameter){
                    $paramName = $parameter->getName();
                    $paramType = $parameter->getType();

                    /**
                     * Valida tipo int
                     */
                    if($paramType->getName() == 'int' && !is_numeric($route['params'][$paramName])){
                        throw new Exception("Parâmetros informados incorretamente", 500);
                    }

                    $arrParams[$paramName] = $route['params'][$paramName] ?? '';
                }


                if(count($arrParams) !== $reflection->getNumberOfRequiredParameters()){
                    throw new Exception('Parâmetros informados incorretamente', 422);
                }

                /**
                 * Sempre passar a classe request mesmo que não seja chamada
                 */
                $arrParams['request'] = $this->request;

                return call_user_func_array($route['closure'], $arrParams);
            }
        }catch(Exception $ex){
            /**
             * Caso a rota inicie com api/ sempre será tratada como um requisição de api e o retorno será em formato json
             */
            if(preg_match($patternApi, $this->request->getRequestedUri())){
                return $this->jsonResponse($ex->getCode(), $ex->getMessage());
            }
            return BaseController::error($ex->getCode(), $ex->getMessage());
        }
        /**
         * Caso a rota inicie com api/ sempre será tratada como um requisição de api e o retorno será em formato json
         */
        if(preg_match($patternApi, $this->request->getRequestedUri())){
            return $this->jsonResponse(404, 'endpoint não encontrado');
        }
        return BaseController::error(404);
    }

    /**
     * Resposta em formato json caso a rota solicitada seja uma api
     * @param int $codigo
     * @param string $message
     */
    private function jsonResponse($codigo = 404, $message = ""){
        $response = new Response();
        $response->setContentType('application/json');
        $response->setCode($codigo);
        $response->setMessage($message);
        return $response->sendResponse();
    }

}