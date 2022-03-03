<?php

namespace App\Controllers\Api;

use App\Entity\RoomEntity;
use App\Models\RoomModel;
use App\Models\RoomSchedulingModel;
use App\system\http\Request;
use App\system\http\Response;
use App\system\Utils\Utils;

/**
 * @OA\Server(
 *     url="http://localhost/rooms",
 *     description="API server"
 * )
 * @OA\Info(
 *     title="Api Salas / Agendamentos",
 *     description="Api de gerenciamento de salas",
 *     version="1.0"
 * )
 * @OA\Tag(
 *     name="salas",
 *     description="Gerenciamento das salas"
 * )
 */
class Room
{

    /**
     * @OA\Post(
     *     path="/api/salas/registrar",tags={"salas"},
     *     @OA\Response(
     *         response="200",
     *         description="Success (Retorna os dados do registro realizado)",
     *         @OA\JsonContent(ref="#/components/schemas/RoomEntity",
     *         example={"code":200, "message": "Sala 'Nova sala' registrada com sucesso", "data":{"id": 1,"name": "Nova Sala","description": "Descrição da sala ...","created_at": "2022-03-03 00:18:03","updated_at": null}})
     *      ),
     *     @OA\Response(
     *         response="422",
     *         description="Error: Unprocessable Entity (Exemplo de resposta caso a propriedade 'name' não seja enviada)",
     *         @OA\JsonContent(ref="#/components/schemas/RoomEntity",
     *         example={"code":422, "message": "Houve um erro durante a tentativa de registro de sala", "data":{},"errors": {"O campo 'nome' é obrigatório!"}})
     *      ),
     *     @OA\RequestBody(
     *         description="O dado 'description' não é obrigtório, porém o dado 'nome' é!",
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/RoomEntity",example={"name":"Nova Sala", "description": "Descrição da sala ..."})
     *     )
     * )
     * @param Request $request
     * @throws \Exception
     */
    static function create(Request $request)
    {
        $response = new Response();
        $response->setContentType('application/json');

        $roomEntity = new RoomEntity();
        $roomEntity->setName($request->getJsonParams('name'));
        $roomEntity->setDescription($request->getJsonParams('description'));
        $roomEntity->setCreatedAt(date('Y-m-d H:i:s'));

        /**
         * Validação create
         */
        $errors = self::validateCreate($roomEntity);
        if (!empty($errors)) {
            $response->setCode(422);
            $response->setContentType('application/json');
            $response->setMessage('Houve um erro durante a tentativa de registro de sala');
            $response->setErrors($errors);
            $response->sendResponse();
        }

        /**
         * Criando sala
         */
        $roomModel = new RoomModel();
        $insertedId = $roomModel->create($roomEntity);

        if ($insertedId) {
            $data = $roomModel->getById($insertedId);
            if (!empty($data)) {
                $data = Utils::convertEntityToArray($data);
            }

            /**
             * Resposta
             */

            $response->setContentType('application/json');
            $response->setContent($data);
            $response->setMessage("Sala '{$data['name']}' registrada com sucesso");
            $response->setCode(200);
            return $response->sendResponse();
        }

        $response->setContentType('application/json');
        $response->setMessage("Houve um erro desconhecido durante a tentativa de cadastro da sala, por favor tente novamente mais tarde");
        $response->setCode(500);
        $response->sendResponse();
    }


    /**
     *
     * Validação de criação
     * @param RoomEntity $roomEntity
     * @return array
     */
    private static function validateCreate(RoomEntity $roomEntity): array
    {
        $errors = array();

        if (empty($roomEntity->getName())) {
            array_push($errors, "O campo 'nome' é obrigatório!");
        } else {
            if (mb_strlen($roomEntity->getName()) < 3 || mb_strlen($roomEntity->getName()) > 150) {
                array_push($errors, "O campo 'nome' deve conter entre 3 e 150 carácteres");
            }
        }

        if (!empty($roomEntity->getDescription()) && mb_strlen($roomEntity->getDescription()) > 255) {
            array_push($errors, "O campo 'descrição' deve conter no máximo 255 carácteres");
        }


        return $errors;
    }

    /**
     * @OA\Get(
     *     path="/api/salas/show",tags={"salas"},
     *     @OA\Response(
     *          response="200",
     *          description="Success",
     *          @OA\JsonContent(ref="#/components/schemas/RoomEntity",example={"code": 200,"message": "Busca realizada com sucesso","data": {"1":{"id": "1","name": "Sala de jogos","description": "Sala voltada para jogos online","created_at": "2022-03-02 06:45:21","updated_at": null},"2":{"id": "2","name": "Sala de informática","description": "Sala para aulas de informática","created_at": "2022-03-02 06:45:44","updated_at": null}},"count_registers": 2}),
     *     ),
     *     @OA\Response(
     *          response="404",
     *          description="Not Found (Resposta que pode ocorrer caso não existam dados no banco)",
     *          @OA\JsonContent(ref="#/components/schemas/RoomEntity",example={"code": 404,"message": "Não foram encontrados registros para essa requisição","data": {},"count_registers": 0})
     *     ),
     *     @OA\Response(
     *          response="422",
     *          description="Error: Unprocessable Entity (Resposta que pode ocorrer caso os filtros por período não sejam informados corretamente)",
     *          @OA\JsonContent(ref="#/components/schemas/RoomEntity", example={  "code": 422,"message": "Houve um erro durante a busca de dados","data": {},"errors": {"O campo 'data' informado é inválido","para a busca filtrada é necessário informar o horario inicial","para a busca filtrada é necessário informar o horario final"}}),
     *     ),
     *     @OA\Parameter(
     *         name="filters[name]",
     *         in="query",
     *         description="A busca filtrada por nome da sala é opcional",
     *         required=false,
     *         @OA\Schema(
     *             default="",
     *             type="string",
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="filters[avaible_rooms][date]",
     *         in="query",
     *         description="Formato requerido: '2022-03-03' | A busca filtrada por período é opcional porém para a realização dessa filtragem é necessário informar os seguintes campos: start_time, end_time e date, caso contrário se algum dos campos mencionados for informado a validação do filtro falhará e nenhum dado será retornado!",
     *         required=false,
     *         @OA\Schema(
     *             type="string",
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="filters[avaible_rooms][start_time]",
     *         in="query",
     *         description="Formato requerido: '00:00' | A busca filtrada por período é opcional porém para a realização dessa filtragem é necessário informar os seguintes campos: start_time, end_time e date, caso contrário se algum dos campos mencionados for informado a validação do filtro falhará e nenhum dado será retornado!",
     *         required=false,
     *         @OA\Schema(
     *             type="string",
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="filters[avaible_rooms][end_time]",
     *         in="query",
     *         description="Formato requerido: '00:00' | A busca filtrada por período é opcional porém para a realização dessa filtragem é necessário informar os seguintes campos: start_time, end_time e date, caso contrário se algum dos campos mencionados for informado a validação do filtro falhará e nenhum dado será retornado!",
     *         required=false,
     *         @OA\Schema(
     *             type="string",
     *         )
     *     )
     * )
     * @OA\Get(
     *     path="/api/salas/show/{idSala}",tags={"salas"},
     *     @OA\Parameter(
     *         description="Id do registro da sala a ser buscada",
     *         in="path",
     *         name="idSala",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64",
     *         )
     *     ),
     *     @OA\Response(
     *          response="200",
     *          description="Success",
     *          @OA\JsonContent(ref="#/components/schemas/RoomEntity", example={"code": 200,"message": "Sucesso na busca","data": {"id": 22,"name": "Sala de reunião","description": "Sala para reuniões","created_at": "2022-03-02 06:45:44","updated_at": null}}),
     *     ),
     *     @OA\Response(
     *          response="404",
     *          description="Not Found (Resposta que pode ocorrer caso um id inexistente no banco seja enviado)",
     *          @OA\JsonContent(ref="#/components/schemas/RoomEntity", example={"code": 404,"message": "Não foram encontrados registros para essa requisição","data": {},"count_registers": 0}),
     *     ),
     *
     * )
     * Endpoint de busca de salas
     * @param Request $request
     * @param int $idSala
     * @throws \Exception
     */
    static function show(Request $request, int $idSala = 0)
    {
        $response = new Response();

        $model = new RoomModel();

        /**
         * Validação de filtros caso sejam enviados
         */
        $filters = $request->getGetParams('filters');
        if (!empty($filters)) {
            if (!is_array($filters)) {
                $response->setCode(422);
                $response->setContentType('application/json');
                $response->setMessage('Houve um erro durante a busca de dados');
                $response->setErrors(array('para a busca filtrada é necessário informar os filtros em formato array'));
                return $response->sendResponse();
            }

            $errors = self::validateFilters($filters);
            if (!empty($errors)) {
                $response->setCode(422);
                $response->setContentType('application/json');
                $response->setMessage('Houve um erro durante a busca de dados');
                $response->setErrors($errors);
                return $response->sendResponse();
            }
        }

        /**
         * Busca
         */
        $otherContent = array('count_registers' => 0);
        if (empty($idSala)) {
            $data = $model->getAll($filters);
            $otherContent['count_registers'] = count($data);
        } else {
            $data = $model->getById($idSala);
        }

        if (!empty($data)) {
            /**
             * Convertendo entidade para array
             */
            $data = Utils::convertEntityToArray($data);
        }

        /**
         * Resposta de sucesso
         */
        $response->setContentType('application/json');
        $response->setContent($data);
        $code = empty($data) ? 404 : 200;
        $message = empty($data) ? 'Não foram encontrados registros para essa requisição' : 'Busca realizada com sucesso';
        $response->setCode($code);
        $response->setMessage($message);
        return $response->sendResponse($otherContent);
    }

    /**
     * Validação dos filtros enviados
     * @param array $filters
     * @return array
     */
    private static function validateFilters(array $filters): array
    {
        $errors = array();

        if (isset($filters['avaible_rooms'])) {
            if (!isset($filters['avaible_rooms']['date'])) {
                array_push($errors, 'para a busca filtrada é necessário informar a data');
            } elseif (!Utils::validateDateFormat($filters['avaible_rooms']['date'], 'Y-m-d')) {
                array_push($errors, "O campo 'data' informado é inválido");
            }

            if (empty($filters['avaible_rooms']['start_time'])) {
                array_push($errors, 'para a busca filtrada é necessário informar o horario inicial');
            } elseif (!Utils::validateDateFormat($filters['avaible_rooms']['start_time'], 'H:i')) {
                array_push($errors, "O campo 'horario inicial' informado é inválido");
            }

            if (empty($filters['avaible_rooms']['end_time'])) {
                array_push($errors, 'para a busca filtrada é necessário informar o horario final');
            } elseif (!Utils::validateDateFormat($filters['avaible_rooms']['end_time'], 'H:i')) {
                array_push($errors, "O campo 'horario final' informado é inválido");
            }
        }

        return $errors;
    }

    /**
     * @OA\Delete(
     *     path="/api/salas/delete/{idSala}",tags={"salas"},
     *     @OA\Parameter(
     *         description="Id do registro da sala a ser deletada",
     *         in="path",
     *         name="idSala",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64",
     *         )
     *     ),
     *     @OA\Response(
     *          response="200",
     *          description="Success",
     *          @OA\JsonContent(ref="#/components/schemas/RoomEntity",example={"code": 200,"message": "Registro excluído com sucesso","data": {}})
     *     ),
     *     @OA\Response(
     *          response="404",
     *          description="Error: Not Found (Resposta exibida quando o registro a ser deletado não existe)",
     *          @OA\JsonContent(ref="#/components/schemas/RoomEntity",example={"code": 404,"message": "Registro inexistente","data": {}})
     *     ),
     *     @OA\Response(
     *          response="422",
     *          description="Error: Unprocessable Entity (Resposta exibida quando o registro a ser deletado possui foreign keys associados a ele na tabela de agendamentos)",
     *          @OA\JsonContent(ref="#/components/schemas/RoomEntity",example={  "code": 422,"message": "Houve um erro durante a tentativa de exclusão do registro","data": {},"errors": {"Existem agendamentos pendentes para esta sala!"}})
     *     ),
     * )
     * Endpoint de exclusão de dados
     * @param int $id
     */
    static function delete(int $id)
    {
        $response = new Response();
        $response->setContentType('application/json');

        $model = new RoomModel();

        if (!$model->existsById($id)) {
            $response->setCode(404);
            $response->setMessage('Registro inexistente');
            return $response->sendResponse();
        } else if ((new RoomSchedulingModel())->hasPendingSchedulingByIdRoom($id)) {
            $response->setCode(422);
            $response->setMessage('Houve um erro durante a tentativa de exclusão do registro');
            $response->setErrors(array('Existem agendamentos pendentes para esta sala!'));
            return $response->sendResponse();
        }

        if ($model->remove($id)) {
            $response->setMessage('Registro excluído com sucesso');
            return $response->sendResponse();
        }

        $response->setCode(500);
        $response->setMessage('Houve um erro durante a tentativa de exclusão do registro');
        $response->setErrors(array('Erro desconhecido'));
        return $response->sendResponse();
    }

}