<?php

namespace App\Models;

use App\Entity\RoomEntity;
use App\system\dao\Connection;
use \PDO;

class RoomModel extends Connection
{

    private $entityClass = null;

    public function __construct($db = 'default')
    {
        parent::__construct($db);
        $this->table = "rooms";
        $this->entityClass = RoomEntity::class;
    }

    /**
     * Retorna todos os dados
     * @return RoomEntity[]|false
     */
    function getAll(array $filters = null)
    {
        $bindValue = [];
        $where = [];

        $query = "SELECT {$this->table}.id as idIndex,{$this->table}.* FROM {$this->table}";

        /**
         * Aplicação dos filtros caso existam
         */
        if (!empty($filters)) {
            if(isset($filters['name'])){
                $where[] = "{$this->table}.name like :name";
                $bindValue[':name'] = '%'.$filters['name'].'%';
            }

            if(isset($filters['avaible_rooms'])){
                $query .= " LEFT JOIN room_scheduling rs ON rs.id_room = {$this->table}.id ";
                $where[] = "(rs.id IS NULL OR NOT(rs.start_time  BETWEEN :start_time AND :end_time OR rs.end_time BETWEEN :start_time AND :end_time))";
                $bindValue[':date'] = $filters['avaible_rooms']['date'];
                $bindValue[':start_time'] = $filters['avaible_rooms']['start_time'];
                $bindValue[':end_time'] = $filters['avaible_rooms']['end_time'];
            }
        }

        if(!empty($where)){
            $query .= " WHERE  ";
            $query .= implode(" AND ", $where);
        }


        $stmt = $this->pdo->prepare($query);

        /**
         * Aplicação dos filtros caso existam
         */
        if(!empty($bindValue)){
            foreach ($bindValue as $paramName => $value){
                $stmt->bindValue($paramName, $value);
            }
        }

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_UNIQUE, $this->entityClass);
    }

    function create(RoomEntity $roomEntity)
    {
        return self::insert($roomEntity);
    }

    /**
     * Retorna por id
     * @param int $id
     * @return mixed
     */
    function getById(int $id)
    {
        if (empty($id)) {
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