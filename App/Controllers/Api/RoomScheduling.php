<?php
namespace App\Controllers\Api;

use App\Entity\RoomSchedulingEntity;
use App\Models\RoomModel;
use App\Models\RoomSchedulingModel;
use App\system\http\Request;
use App\system\http\Response;
use App\system\Utils\Security;
use App\system\Utils\Utils;

/**
 * @OA\Tag(
 *     name="agendamento",
 *     description="Gerenciamento dos agendamentos"
 * )
 */
class RoomScheduling{


    /**
     * @OA\Post(
     *     path="/api/agendamento/registrar",tags={"agendamento"},
     *     @OA\Response(
     *         response="200",
     *         description="Success (Retorna os dados do registro realizado)",
     *         @OA\JsonContent(ref="#/components/schemas/RoomSchedulingEntity",
     *         example={"code": 200,"message": "Agendamento realizado com sucesso","data": {"id": 15,"id_room": 21,"date": "2022-10-10","start_time": "00:00:00","end_time": "00:30:00","created_at": "2022-03-03 04:04:26","updated_at": null}})
     *      ),
     *     @OA\Response(
     *         response="422",
     *         description="Error: Unprocessable Entity (1- Exemplo de resposta caso os dados obrigatórios não sejam enviados, 2- Exemplo de resposta caso a sala solicitada já esteja agendada dentro do horário solicitado)",
     *         @OA\JsonContent(ref="#/components/schemas/RoomSchedulingEntity",
     *         example={{  "code": 422,"message": "Houve um erro durante a tentativa de agendamento","data": {},"errors": {"O campo 'data' é obrigatório","O campo 'hora inicial' é obrigatório","O campo 'hora final' é obrigatório","O campo 'id_room' é obrigatório"}},
     *         {"code": 422,"message": "Houve um erro durante a tentativa de agendamento","data": {},"errors": {"Já existe um agendamento para esta sala e data dentro do horário solicitado"}}})
     *      ),
     *     @OA\RequestBody(
     *         description="Todos os dados no exemplo são obrigatórios",
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/RoomSchedulingEntity",
     *          example={"date":"2022-10-10", "start_time": "00:00", "end_time": "00:30", "id_room": 21}),
     *     )
     * )
     * Endpoint de agendamento de sala
     * @param Request $request
     * @throws \Exception
     */
    static function create(Request $request){
        $response = new Response();

        $schedulingEntity = new RoomSchedulingEntity();
        $schedulingEntity->setDate($request->getJsonParams('date'));
        $schedulingEntity->setStartTime($request->getJsonParams('start_time'));
        $schedulingEntity->setEndTime($request->getJsonParams('end_time'));
        $schedulingEntity->setIdRoom($request->getJsonParams('id_room'));

        /**
         * Valida agendamento
         */
        $errors = self::validateCreate($schedulingEntity);
        if(!empty($errors)){
            $response->setCode(422);
            $response->setContentType('application/json');
            $response->setMessage('Houve um erro durante a tentativa de agendamento');
            $response->setErrors($errors);
            return $response->sendResponse();
        }

        /**
         * Registrando
         */
        $schedulingModel = new RoomSchedulingModel();
        $insertedId = $schedulingModel->create($schedulingEntity);
        if($insertedId){
            $data = $schedulingModel->getById($insertedId);
            if(!empty($data)){
                /**
                 * Converte entidade em array
                 */
                $data = Utils::convertEntityToArray($data);
            }

            /**
             * Resposta de sucesso
             */
            $response->setContentType('application/json');
            $response->setMessage("Agendamento realizado com sucesso");
            $response->setContent($data);
            $response->setCode(200);
            return $response->sendResponse();
        }

    }

    /**
     * @OA\Get(
     *     path="/api/agendamento/show",tags={"agendamento"},
     *     @OA\Response(
     *          response="200",
     *          description="Success",
     *          @OA\JsonContent(ref="#/components/schemas/RoomSchedulingEntity",example={"code": 200,"message": "Busca realizada com sucesso","data": {"11": {"id": "11","id_room": "21","date": "2022-03-02","start_time": "10:00:00","end_time": "22:00:00","created_at": "2022-03-02 06:46:56","updated_at": null,"room_name": "Sala de jogos"},"12": {"id": "12","id_room": "21","date": "2022-03-14","start_time": "11:59:00","end_time": "11:59:00","created_at": "2022-03-02 11:54:26","updated_at": null,"room_name": "Sala de jogos"}},"count_registers": 2}),
     *     ),
     *     @OA\Response(
     *          response="404",
     *          description="Not Found (Resposta que pode ocorrer caso não existam dados no banco)",
     *          @OA\JsonContent(ref="#/components/schemas/RoomSchedulingEntity",example={"code": 404,"message": "Não foram encontrados registros para essa requisição","data": {},"count_registers": 0})
     *     )
     * )
     * Endpoint de busca de dados (Não possui filtros)
     * @param Request $request
     * @throws \Exception
     */
    static function show(Request $request){
        $response = new Response();

        $model = new RoomSchedulingModel();

        if(empty($idSala)){
            $data = $model->getAll();
        }else{
            $data = $model->getById($idSala);
        }

        if(!empty($data)){
            $data = Utils::convertEntityToArray($data);
        }


        if(!empty($data)){
            $arrRoomEntity = (new RoomModel())->getAll();
            foreach ($data as $id => $value){
                if(array_key_exists($value['id_room'],$arrRoomEntity)){
                    $data[$id]['room_name'] = $arrRoomEntity[$value['id_room']]->getName();
                }
            }
        }

        $response->setContentType('application/json');
        $response->setContent($data);

        $code = empty($data) ? 404 : 200;
        $message = empty($data) ? 'Não foram encontrados registros para essa requisição' : 'Busca realizada com sucesso';
        $response->setCode($code);
        $response->setMessage($message);

        $response->sendResponse(array('count_registers' => count($data)));
    }

    /**
     * Valida agendamento
     * @param RoomSchedulingEntity $schedulingEntity
     * @return array
     */
    private static function validateCreate(RoomSchedulingEntity $schedulingEntity){
        $errors = array();

        if(empty($schedulingEntity->getDate())){
            array_push($errors, "O campo 'data' é obrigatório");
        }else
        {
            if(!Utils::validateDateFormat($schedulingEntity->getDate(),'Y-m-d')){
                array_push($errors, "O campo 'data' informado é inválido");
            }
        }

        if(empty($schedulingEntity->getStartTime())){
            array_push($errors, "O campo 'hora inicial' é obrigatório");
        }else
        {
            if(!Utils::validateDateFormat($schedulingEntity->getStartTime(),'H:i')){
                array_push($errors, "O campo 'hora inicial' informado é inválido");
            }
        }

        if(empty($schedulingEntity->getEndTime())){
            array_push($errors, "O campo 'hora final' é obrigatório");
        }else
        {
            if(!Utils::validateDateFormat($schedulingEntity->getEndTime(),'H:i')){
                array_push($errors, "O campo 'hora final' informado é inválido");
            }
        }

        if(empty($schedulingEntity->getIdRoom())){
            array_push($errors, "O campo 'id_room' é obrigatório");
        }else
        {
            if(!Security::validateInt($schedulingEntity->getIdRoom())){
                array_push($errors, "O campo 'id_room' precisa ser numérico");
            }else if(!(new RoomModel())->existsById($schedulingEntity->getIdRoom())){
                array_push($errors, "Sala inexistente");
            }
        }


        /**
         * Outras validações dependentes das validações de horario e data
         */
        if(empty($errors)){
            $startDate = strtotime($schedulingEntity->getDate()."".$schedulingEntity->getStartTime());
            $period_start = strtotime($schedulingEntity->getStartTime());
            $period_end = strtotime($schedulingEntity->getEndTime());

            /**
             * Valida horarios (hora final não pode ser menor que hora inicial)
             */
            if($period_end < $period_start){
                array_push($errors, "A hora final não pode ser menor que a hora inicial");
            } else if($startDate < strtotime(date('Y-m-d H:i:s'))){
                /**
                 * Valida se a data e hora do agendamento são maiores que o horário atual
                 */
                array_push($errors, "A data e horário do agendamento não podem ser menores que o momento atual");
            }else if(($period_end - $period_start ) < 1800){
                array_push($errors, "O agendamento minimo é de 30 minitos");
            }else if((new RoomSchedulingModel())->hasScheduling($schedulingEntity)){
                /**
                 * Valida se já existe um agendamento dentro do horário solicitado
                 */
                array_push($errors, "Já existe um agendamento para esta sala e data dentro do horário solicitado");
            }
        }

        return $errors;
    }

    /**
     * @OA\Delete(
     *     path="/api/agendamento/delete/{idAgendamento}",tags={"agendamento"},
     *     @OA\Parameter(
     *         description="Id do registro do agendamento a ser deletado",
     *         in="path",
     *         name="idAgendamento",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             format="int64",
     *         )
     *     ),
     *     @OA\Response(
     *          response="200",
     *          description="Success",
     *          @OA\JsonContent(ref="#/components/schemas/RoomSchedulingEntity",example={"code": 200,"message": "Registro excluído com sucesso","data": {}})
     *     ),
     *     @OA\Response(
     *          response="404",
     *          description="Error: Not Found (Resposta exibida quando o registro a ser deletado não existe)",
     *          @OA\JsonContent(ref="#/components/schemas/RoomSchedulingEntity",example={"code": 404,"message": "Registro inexistente","data": {}})
     *     )
     * )
     * Endpoint de exclusão de agendamento
     * @param int $id
     */
    static function delete(int $id){
        $response = new Response();
        $response->setContentType('application/json');

        $model = new RoomSchedulingModel();

        if(!$model->existsById($id)){
            $response->setMessage('Registro inexistente');
            return $response->sendResponse();
        }

        if($model->remove($id)){
            $response->setMessage('Registro excluído com sucesso');
            return $response->sendResponse();
        }

        $response->setCode(500);
        $response->setMessage('Houve um erro durante a tentativa de exclusão do registro');
        $response->setErrors(array('Erro desconhecido'));
        return $response->sendResponse();
    }
}