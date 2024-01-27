<?php

namespace App\Repository;

use App\Entity\Car;
use App\Entity\Review;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ReviewRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Review::class);
    }

    public function findLatestHighRatedReviews(Car $car, int $limit = 5): array
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.car = :car')
            ->andWhere('r.starRating > 6')
            ->setParameter('car', $car)
            ->orderBy('r.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
}