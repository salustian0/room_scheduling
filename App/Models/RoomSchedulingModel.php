<?php
namespace App\Models;
use App\Entity\RoomSchedulingEntity;
use App\system\dao\Connection;
use \PDO;

class RoomSchedulingModel extends  Connection {

    private $entityClass = null;
    public function __construct($db = 'default')
    {
        parent::__construct($db);
        $this->table = "room_scheduling";
        $this->entityClass = RoomSchedulingEntity::class;
    }

    /**
     * Retorna todos os dados
     * @return RoomSchedulingEntity[]|false
     */
    function getAll(){
       $query = "SELECT {$this->table}.id as idIndex,{$this->table}.* FROM {$this->table}";
       $stmt = $this->pdo->prepare($query);
       $stmt->execute();
       return $stmt->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_UNIQUE , $this->entityClass);
    }

    /**
     * @param RoomSchedulingEntity $roomEntity
     * @return false|string
     * @throws \Exception
     */
    function create(RoomSchedulingEntity $roomEntity){
        return self::insert($roomEntity);
    }

    /**
     * Retorna por id
     * @param int $id
     * @return mixed
     */
    function getById(int $id){
        if(empty($id)){
            return false;
        }
        $query = "SELECT {$this->table}.* FROM {$this->table} WHERE {$this->table}.id = :id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchObject($this->entityClass);
    }

    /**
     * Verifica existencia por id
     * @param int $id
     * @return bool
     */
    function existsById(int $id): bool
    {
        if (empty($id)) {
            return false;
        }
        $query = "SELECT 1 FROM {$this->table} WHERE {$this->table}.id = :id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    /**
     * Valida se já existe um agendamento para a sala no horário informado
     * @param RoomSchedulingEntity $roomSchedulingEntity
     * @return bool
     */
    function hasScheduling(RoomSchedulingEntity $roomSchedulingEntity){
        $query = "SELECT 1 FROM {$this->table} WHERE date = :date AND :start_time BETWEEN start_time AND end_time AND id_room = :id_room LIMIT 1";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindValue(':date', $roomSchedulingEntity->getDate());
        $stmt->bindValue(':start_time', $roomSchedulingEntity->getStartTime().":00");
        $stmt->bindValue(':id_room', $roomSchedulingEntity->getIdRoom());
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    /**
     * @param int $idRoom
     * @return false|mixed
     */
    function hasPendingSchedulingByIdRoom(int $idRoom){
        if(empty($idRoom)){
            return false;
        }
        $query = "SELECT 1 FROM {$this->table} WHERE {$this->table}.id_room = :id_room AND NOW() < concat({$this->table}.date,' ',{$this->table}.end_time)";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindValue(':id_room', $idRoom, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    /**
     * @param int $id
     * @return bool
     */
    function remove(int $id){

        if(empty($id)){
            return false;
        }

        $query = "DELETE FROM {$this->table} WHERE id = :id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindValue(':id',$id,PDO::PARAM_INT);
        return $stmt->execute();
    }

}