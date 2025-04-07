<?php

namespace App\Repositories;

use Doctrine\ORM\EntityRepository;

class CommentRepository extends EntityRepository
{
    public function findAllComments()
    {
        return $this->findAll();
    }
}
