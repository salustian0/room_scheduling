<?php
namespace App\Entity;
/**
 * @OA\Schema()
 */
class RoomSchedulingEntity implements EntityInterface{
    /**
     * @OA\Property (type="integer", description="Id do agendamento", nullable="false")
     * @var $id
     */
    private $id;
    /**
     * @OA\Property (type="integer", description="Id da sala", nullable="false")
     * @var $id_room
     */
    private $id_room;
    /**
     * @OA\Property (type="string", description="data do agendamento", nullable="false")
     * @var $date
     */
    private $date;
    /**
     * @OA\Property (type="string", description="Horário inicial do agendamento", nullable="false")
     * @var $start_time
     */
    private $start_time;
    /**
     * @OA\Property (type="string", description="Horário final do agendamento", nullable="false")
     * @var $end_time
     */
    private $end_time;
    /**
     * @OA\Property (type="string", description="Data de criação do registro", nullable="false")
     * @var $created_at
     */
    private $created_at;
    /**
     * @OA\Property (type="string", description="Data da última atualização do registro", nullable="false")
     * @var $updated_at
     */
    private $updated_at;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getIdRoom()
    {
        return $this->id_room;
    }

    /**
     * @param mixed $id_room
     */
    public function setIdRoom($id_room): void
    {
        $this->id_room = $id_room;
    }

    /**
     * @return mixed
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param mixed $date
     */
    public function setDate($date): void
    {
        $this->date = $date;
    }

    /**
     * @return mixed
     */
    public function getStartTime()
    {
        return $this->start_time;
    }

    /**
     * @param mixed $start_time
     */
    public function setStartTime($start_time): void
    {
        $this->start_time = $start_time;
    }

    /**
     * @return mixed
     */
    public function getEndTime()
    {
        return $this->end_time;
    }

    /**
     * @param mixed $end_time
     */
    public function setEndTime($end_time): void
    {
        $this->end_time = $end_time;
    }

    /**
     * @return mixed
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }

    /**
     * @param mixed $created_at
     */
    public function setCreatedAt($created_at): void
    {
        $this->created_at = $created_at;
    }

    /**
     * @return mixed
     */
    public function getUpdatedAt()
    {
        return $this->updated_at;
    }

    /**
     * @param mixed $updated_at
     */
    public function setUpdatedAt($updated_at): void
    {
        $this->updated_at = $updated_at;
    }

}