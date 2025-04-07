<?php

namespace App\Repositories;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;

class NotificationRepository extends EntityRepository
{
    public function getAllNotificationsByFilter(array $data, int $itemsPerPage, int $page): array
    {
        $qb = $this->createQueryBuilder('n');

        if (isset($data['title'])) {
            $qb->andWhere('n.title LIKE :title')
                ->setParameter('title', '%'.$data['title'].'%');
        }
        if (isset($data['is_read'])) {
            $qb->andWhere('n.is_read = :is_read')
                ->setParameter('is_read', filter_var($data['is_read'], FILTER_VALIDATE_BOOLEAN));
        }
        $qb->orderBy('n.created_at', 'DESC');

        $paginator = new Paginator($qb);
        $totalItems = count($paginator);
        $totalPageCount = (int)ceil($totalItems / $itemsPerPage);

        $qb->setFirstResult($itemsPerPage * ($page - 1))
            ->setMaxResults($itemsPerPage);

        return [
            'notifications' => $qb->getQuery()->getResult(),
            'totalPageCount' => $totalPageCount,
            'totalItems' => $totalItems,
        ];
    }
}
