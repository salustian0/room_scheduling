<?php

namespace App\system\dao;

use \PDO;
use PDOStatement;

abstract class Connection
{

    private string $db_host;
    private string $db_name;
    private string $user;
    private string $password;
    protected string $table;

    protected PDO $pdo;
    protected PdoStatement $statement;


    public function __construct($db = 'default')
    {
        if (!defined("DB_SETTINGS") || !isset(DB_SETTINGS[$db])) {
            throw new \Exception("Configuração de banco de dados \"{$db}\" não encontrada!");
        }

        $dbSettings = DB_SETTINGS[$db] ?? [];
        $this->db_host = $dbSettings['DB_HOST'] ?? "";
        $this->db_name = $dbSettings['DB_NAME'] ?? "";
        $this->user = $dbSettings['DB_USER'] ?? "";
        $this->password = $dbSettings['DB_PASS'] ?? "";

        $this->connect();
    }

    /**
     * Conexão ao banco
     */
    private function connect()
    {
        try {
            $this->pdo = new PDO("mysql:host={$this->db_host};dbname={$this->db_name};charset=utf8", $this->user, $this->password);
        } catch (\PDOException $ex) {
            die("Houve um erro durante a conexão ao banco de dados: {$ex->getMessage()}");
        }
    }

    /**
     * Atualização de dados
     * @param $arrParams
     * @return bool
     */
    protected function update($entity, $where){
        $arrEntity = $this->convertEntityToArray($entity);

        $bindValues = [];
        /**
         * Sintaxe inicial da query
         */
        $query = "UPDATE {$this->table} SET ";
        /**
         * Sintaxe column = :column, column2 = :column2
         */
        $aux = [];
        foreach ($arrEntity as $key => $value){
            $bindValues[$key] = $value;
            $aux[] =  "{$key} = :{$key}";
        }
        $query .= implode(',',$aux);
        $this->getWhereSyntax($query, $bindValues, $where);
        $this->statement = $this->pdo->prepare($query);

        foreach ($bindValues as $key => $value){
            if(is_array($value) && isset($value['type'])){
                $this->statement->bindValue(":{$key}" , $value['value'], $value['type']);
                continue;
            }
            $this->statement->bindValue(":{$key}" , $value);
        }

        return $this->statement->execute();
    }

    /**
     * Inserção de dados
     * @param $entity
     * @return false|string
     * @throws \Exception
     */
    protected function insert($entity){
        $arrEntity = $this->convertEntityToArray($entity);
        $arrValues = array_map(function($v){
            return ":{$v}";
        },array_keys($arrEntity));

        /**
         * Sintaxe inicial da query
         */
        $query = "INSERT INTO {$this->table}(".implode(', ', array_keys($arrEntity)).")VALUES(".implode(', ', $arrValues).")";
        $this->statement = $this->pdo->prepare($query);

        foreach ($arrEntity as $key => $value){
            if(is_array($value) && isset($value['type'])){
                $this->statement->bindValue(":{$key}" , $value['value'], $value['type']);
                continue;
            }
            $this->statement->bindValue(":{$key}" , $value);
        }

        if($this->statement->execute()){
            return $this->pdo->lastInsertId();
        }
        return false;
    }


    /**
     * Método responsável por converter uma entidade em array associativo
     * @param $entity
     * @return array
     * @throws \Exception
     */
    private function convertEntityToArray($entity) : array{
        if(gettype($entity) !== 'object'){
            throw new \Exception("Houve um erro na tentativa de atualização dos dados, por favor tente novamente mais tarde", 500);
        }
        /**
         * cast
         */
        $arrEntity = (array)$entity;
        /**
         * Filtro (somente são permitidos indices com dados)
         */
        $arrEntity = array_filter($arrEntity);
        $arrParams = array();
        foreach ($arrEntity as $key => $value){
            /**
             * Replace no nome da chave ex: 'App\Entity\UserEntityid' => 'id'
             */
            $newKey = str_replace(get_class($entity), '', $key);
            $newKey = trim($newKey);

            /**
             * Remove id ou created_at (colunas que não podem ser alteradas)
             */

            if(in_array($newKey, ['id', 'created_at'])){
                continue;
            }
            $arrParams[$newKey] = $value;
        }

        return $arrParams;
    }

    /**
     * Cria sintaxe where
     * @param $query
     * @param $bindValue
     * @param $where
     */
    private function getWhereSyntax(&$query, &$bindValues, $where){
        /**
         * Sintaxe Where
         */
        if(!empty($where)){
            $aux = [];
            $query .= " WHERE ";
            if(is_array($where)){
                foreach ($where as $key => $value){
                    $aux[] = "{$key} = :{$key}";
                    $bindValues[$key] = $value;
                }
                $query .= implode(" AND ", $aux);
            }else if(is_string($where)){
                $query .= $where;
            }
        }
    }

}