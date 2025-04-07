<?php

namespace App\Repositories;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;

class ReactionRepository extends EntityRepository
{
    public function getAllReactionsByFilter(array $data, int $itemsPerPage, int $page): array
    {
        $qb = $this->createQueryBuilder('r');

        if (isset($data['type'])) {
            $qb->andWhere('r.type = :type')
                ->setParameter('type', $data['type']);
        }
        $qb->orderBy('r.createdAt', 'DESC');

        $paginator = new Paginator($qb);
        $totalItems = count($paginator);
        $totalPageCount = (int)ceil($totalItems / $itemsPerPage);

        $qb->setFirstResult($itemsPerPage * ($page - 1))
            ->setMaxResults($itemsPerPage);

        return [
            'reactions' => $qb->getQuery()->getResult(),
            'totalPageCount' => $totalPageCount,
            'totalItems' => $totalItems,
        ];
    }
}
