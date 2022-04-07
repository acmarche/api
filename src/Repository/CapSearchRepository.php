<?php

namespace AcMarche\Api\Repository;

use AcMarche\Api\Entity\CapSearch;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CapSearch|null find($id, $lockMode = null, $lockVersion = null)
 * @method CapSearch|null findOneBy(array $criteria, array $orderBy = null)
 * @method CapSearch[]    findAll()
 * @method CapSearch[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CapSearchRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CapSearch::class);
    }

    public function remove(CapSearch $search): void
    {
        $this->_em->remove($search);
    }

    public function flush(): void
    {
        $this->_em->flush();
    }

    public function persist(CapSearch $search): void
    {
        $this->_em->persist($search);
    }
}
