<?php

namespace AcMarche\Api\Repository;

use AcMarche\Api\Doctrine\OrmCrudTrait;
use AcMarche\Api\Entity\AccessToken;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method AccessToken|null find($id, $lockMode = null, $lockVersion = null)
 * @method AccessToken|null findOneBy(array $criteria, array $orderBy = null)
 * @method AccessToken[]    findAll()
 * @method AccessToken[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AccessTokenRepository extends ServiceEntityRepository
{
    use OrmCrudTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AccessToken::class);
    }

    public function findOneByToken(string $accessToken): ?AccessToken
    {
        return $this
            ->createQueryBuilder('access_token')
            ->andWhere('access_token.token = :accessToken')
            ->setParameter('accessToken', $accessToken)
            ->getQuery()->getOneOrNullResult();
    }

}