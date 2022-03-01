<?php

namespace App\system\http;
use App\Controllers\BaseController;
use \Closure;
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
    public function addGetRoute($path, Closure $closure, bool $api = false)
    {
        self::registerRoute('GET', $path, $closure, $api);
    }

    /**
     * Adiciona uma rota do tipo POST
     * @param $path
     * @param Closure $closure
     * @param array $params
     */
    public function addPostRoute($path, Closure $closure, bool $api = false)
    {
        self::registerRoute('POST', $path, $closure, $api);
    }

    /**
     * Converte o path em regex e registra a rota
     * @param $type
     * @param $path
     * @param Closure $closure
     */
    private function registerRoute($type, $path, Closure $closure, bool $api)
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
            'api' => $api
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

                if($route['api'] === false){
                    $route['params'] = array_filter(array_combine($keys, $matches));
                }

                $route['type'] = 'route';
                return $route;
            }
        }
        return [];
    }

    public function run()
    {
        try{
            $route = $this->getRoute();
            if(!empty($route)){
                $reflection = new ReflectionFunction($route['closure']);
                $arrParams = [];


                foreach ($reflection->getParameters() as $parameter){
                    $paramName = $parameter->getName();
                    $arrParams[$paramName] = $route['params'][$paramName] ?? '';
                }

                if(count($arrParams) !== $reflection->getNumberOfRequiredParameters()){
                    throw new Exception('Parâmetros informados incorretamente', 422);
                }



                /**
                 * Sempre passar a request
                 */
                $arrParams['request'] = $this->request;
                return call_user_func_array($route['closure'], $arrParams);
            }
        }catch(Exception $ex){
            return BaseController::error($ex->getCode(), $ex->getMessage());
        }



        return BaseController::error(404);
    }

}