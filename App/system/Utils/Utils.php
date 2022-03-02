<?php
namespace App\system\Utils;
class Utils{

    /**
     * Valida formato de data
     * @param $date
     * @param string $format
     * @return bool
     */
    static function validateDateFormat($date, $format='Y-m-d H:i:s'){
        $dateObj =  \DateTime::createFromFormat($format,$date);
        return $dateObj && $dateObj->format($format) == $date;
    }

    static function formatDate($date, $format = 'd/m/Y'){
        return date($format,strtotime($date));
    }

    /**
     * Valida se o dado é do tipo inteiro
     * @param $arg
     * @return mixed
     */
    static function validateInt($arg){
        return filter_var($arg, FILTER_VALIDATE_INT);
    }

    /**
     * Método responsável por converter uma entidade em array associativo
     * @param $entity
     * @return array
     * @throws \Exception
     */
    public static function convertEntityToArray($entity, $removeNulls = false) : array{

        if(is_array($entity)){
            $arrResult = [];
            foreach ($entity as $key  => $value){
                if(gettype($value) !== 'object'){
                    return [];
                }
                $arrResult[$key] = self::convertEntityToArray($value,$removeNulls);
            }
            return $arrResult;
        }

        if(gettype($entity) !== 'object'){
            return [];
        }
        /**
         * cast
         */
        $arrEntity = (array)$entity;
        if($removeNulls){
            $arrEntity = array_filter($entity);
        }
        /**
         * Filtro (somente são permitidos indices com dados)
         */

        $arrParams = array();
        foreach ($arrEntity as $key => $value){
            /**
             * Remove nome da class ex:'{App\Entity\UserEntity}id' => 'id'
             */
            $newKey = str_replace(get_class($entity), '', $key);
            $newKey = trim($newKey);
            /**
             * Remove id ou created_at (colunas que não podem ser alteradas)
             */

            $arrParams[$newKey] = $value;
        }

        return $arrParams;
    }
}