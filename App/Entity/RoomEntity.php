<?php
namespace App\Entity;

/**
 * @OA\Schema()
 */
class RoomEntity implements EntityInterface {
    /**
     * @OA\Property (type="integer", description="Id da sala", nullable="false")
     * @var int $id
     */
    private $id;
    /**
     * @OA\Property (type="string", description="Nome da sala", nullable="false")
     * @var string $name
     */
    private $name;
    /**
     * @OA\Property (type="string", description="Descrição da sala", nullable="true")
     * @var string $description
     */
    private $description;
    /**
     * @OA\Property (type="string", description="Data de criação da sala", nullable="false")
     * @var string $description
     */
    private $created_at;
    /**
     * @OA\Property (type="string", description="Data da última atualização da sala", nullable="true")
     * @var string $description
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
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name): void
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description): void
    {
        $this->description = $description;
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