<?php

namespace App\Repositories;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;

class UserRepository extends EntityRepository
{
    public function getAllUsersByFilter(array $data, int $itemsPerPage, int $page): array
    {
        $qb = $this->createQueryBuilder('u');

        if (isset($data['username'])) {
            $qb->andWhere('u.username LIKE :username')
                ->setParameter('username', '%'.$data['username'].'%');
        }
        if (isset($data['email'])) {
            $qb->andWhere('u.email LIKE :email')
                ->setParameter('email', '%'.$data['email'].'%');
        }
        $qb->orderBy('u.createdAt', 'DESC');

        $paginator = new Paginator($qb);
        $totalItems = count($paginator);
        $totalPageCount = (int)ceil($totalItems / $itemsPerPage);

        $qb->setFirstResult($itemsPerPage * ($page - 1))
            ->setMaxResults($itemsPerPage);

        return [
            'users' => $qb->getQuery()->getResult(),
            'totalPageCount' => $totalPageCount,
            'totalItems' => $totalItems,
        ];
    }
}
