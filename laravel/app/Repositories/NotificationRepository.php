<?php

namespace App\Repositories;

use Doctrine\ORM\EntityRepository;

class NotificationRepository extends EntityRepository
{
    public function findAllNotifications()
    {
        return $this->findAll();
    }
}
