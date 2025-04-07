<?php

namespace App\Repositories;

use Doctrine\ORM\EntityRepository;

class ReactionRepository extends EntityRepository
{
    public function findAllReactions()
    {
        return $this->findAll();
    }
}
