<?php

namespace App\Repositories;

use Doctrine\ORM\EntityRepository;

class PostRepository extends EntityRepository
{
    public function findAllPosts()
    {
        return $this->findAll();
    }
}
