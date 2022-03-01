<?php
namespace App\Entity;

interface EntityInterface {
    public function setId($id);
    public function getId();

    public function setCreatedAt($created_at);
    public function getCreatedAt();

    public function setUpdatedAt($updated_at);
    public function getUpdatedAt();
}