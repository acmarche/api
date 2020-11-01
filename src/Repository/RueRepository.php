<?php

namespace AcMarche\Api\Repository;

use AcMarche\Api\Entity\Rue;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Rue|null find($id, $lockMode = null, $lockVersion = null)
 * @method Rue|null findOneBy(array $criteria, array $orderBy = null)
 * @method Rue[]    findAll()
 * @method Rue[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RueRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Rue::class);
    }
}
