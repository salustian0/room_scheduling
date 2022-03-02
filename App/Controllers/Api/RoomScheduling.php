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
 * Classe responsável pelos agendamentos de salas
 */
class RoomScheduling{

    static function create(Request $request){
        $response = new Response();

        $schedulingEntity = new RoomSchedulingEntity();
        $schedulingEntity->setDate($request->getPostParams('date'));
        $schedulingEntity->setStartTime($request->getPostParams('start_time'));
        $schedulingEntity->setEndTime($request->getPostParams('end_time'));
        $schedulingEntity->setIdRoom($request->getPostParams('id_room'));

        /**
         * Valida agendamento
         */
        $errors = self::validateCreate($schedulingEntity);
        if(!empty($errors)){
            $response->setCode(500);
            $response->setContentType('application/json');
            $response->setMessage('Houve um erro durante a tentativa de agendamento');
            $response->setErrors($errors);
            return $response->sendResponse();
        }

        $schedulingModel = new RoomSchedulingModel();
        $insertedId = $schedulingModel->create($schedulingEntity);

        if($insertedId){
            $data = $schedulingModel->getById($insertedId);
            if(!empty($data)){
                $data = Utils::convertEntityToArray($data);
            }

            /**
             * Resposta
             */
            $response = new Response();
            $response->setContentType('application/json');
            $response->setMessage("Agendamento realizado com sucesso");
            $response->setContent($data);
            $response->setCode(200);
            return $response->sendResponse();
        }

    }

    static function show(Request $request){
        $response = new Response();

        $model = new RoomSchedulingModel();

        $filters = $request->getGetParams('filters');
        if(!empty($filters)){
            if(!is_array($filters)){
                $response->setCode(500);
                $response->setContentType('application/json');
                $response->setMessage('Houve um erro durante a busca de dados');
                $response->setErrors(array('para a busca filtrada é necessário informar os filtros em formato array'));
                return $response->sendResponse();
            }

            $errors = self::validateFilters($filters);
            if(!empty($errors)){
                $response->setCode(500);
                $response->setContentType('application/json');
                $response->setMessage('Houve um erro durante a busca de dados');
                $response->setErrors($errors);
                return $response->sendResponse();
            }
        }

        if(empty($idSala)){
            $data = $model->getAll($filters);
        }else{
            $data = $model->getById($idSala);
        }

        if(!empty($data)){
            $data = Utils::convertEntityToArray($data);
        }


        $arrRoomEntity = (new RoomModel())->getAll();
        foreach ($data as $id => $value){
            if(array_key_exists($value['id_room'],$arrRoomEntity)){
                $data[$id]['room_name'] = $arrRoomEntity[$value['id_room']]->getName();
            }
        }

        $response->setContentType('application/json');
        $response->setContent($data);
        $response->setCode(200);
        $response->sendResponse();
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
            }else if($startDate < strtotime(date('Y-m-d H:i:s'))){
                /**
                 * Valida se a data e hora do agendamento são maiores que o horário atual
                 */
                array_push($errors, "A data e horário do agendamento não podem ser menores que o momento atual");
            }else if((new RoomSchedulingModel())->hasScheduling($schedulingEntity)){
                /**
                 * Valida se já existe um agendamento dentro do horário solicitado
                 */
                array_push($errors, "Já existe um agendamento para esta sala para esta data dentro do horário solicitado");
            }
        }

        return $errors;
    }

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