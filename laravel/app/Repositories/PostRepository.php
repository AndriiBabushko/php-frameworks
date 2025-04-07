<?php

namespace App\Repositories;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;

class PostRepository extends EntityRepository
{
    public function getAllPostsByFilter(array $data, int $itemsPerPage, int $page): array
    {
        $qb = $this->createQueryBuilder('p');

        if (isset($data['content'])) {
            $qb->andWhere('p.content LIKE :content')
                ->setParameter('content', '%'.$data['content'].'%');
        }
        // Приклад: можна також фільтрувати за title або author, якщо потрібно
        $qb->orderBy('p.createdAt', 'DESC');

        $paginator = new Paginator($qb);
        $totalItems = count($paginator);
        $totalPageCount = (int)ceil($totalItems / $itemsPerPage);

        $qb->setFirstResult($itemsPerPage * ($page - 1))
            ->setMaxResults($itemsPerPage);

        return [
            'posts' => $qb->getQuery()->getResult(),
            'totalPageCount' => $totalPageCount,
            'totalItems' => $totalItems,
        ];
    }
}
