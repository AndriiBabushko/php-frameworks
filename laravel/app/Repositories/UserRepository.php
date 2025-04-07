<?php

namespace App\Repositories;

use Doctrine\ORM\EntityRepository;

class UserRepository extends EntityRepository
{
    public function findAllUsers()
    {
        return $this->findAll();
    }
}
