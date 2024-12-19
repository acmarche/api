<?php

namespace AcMarche\Api\Parking\Repository;

use AcMarche\Api\Doctrine\OrmCrudTrait;
use AcMarche\Api\Entity\Parking;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Parking|null find($id, $lockMode = null, $lockVersion = null)
 * @method Parking|null findOneBy(array $criteria, array $orderBy = null)
 * @method Parking[]    findAll()
 * @method Parking[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ParkingRepository extends ServiceEntityRepository
{
    use OrmCrudTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Parking::class);
    }

    public function findByNumber(int $id): ?Parking
    {
        return $this
            ->createQueryBuilder('parking')
            ->where('parking.number = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
