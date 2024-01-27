<?php

namespace App\Repository;

use App\Entity\Color;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Color>
 *
 * @method Color|null find($id, $lockMode = null, $lockVersion = null)
 * @method Color|null findOneBy(array $criteria, array $orderBy = null)
 * @method Color[]    findAll()
 * @method Color[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ColorRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Color::class);
    }

    /**
     * @throws NonUniqueResultException
     */
    public function getRandom(): ?Color
    {
        $queryBuilder = $this->createQueryBuilder('c');
        $queryBuilder->select('c')
            ->orderBy('RANDOM()');

        return $queryBuilder->getQuery()->setMaxResults(1)->getOneOrNullResult();
    }
}
