<?php

namespace App\Repository;

use App\Entity\Coupon;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Coupon|null find($id, $lockMode = null, $lockVersion = null)
 * @method Coupon|null findOneBy(array $criteria, array $orderBy = null)
 * @method Coupon[]    findAll()
 * @method Coupon[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CouponRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Coupon::class);
    }

//    /**
//     * @return Coupon[] Returns an array of Coupon objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Coupon
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    public function findByCampaignId($campaignId)
    {
        return $this->findBy(['campaign' => $campaignId]);
    }

    public function findOneByCampaignIdAndId($campaignId, $id)
    {
        return $this->findOneBy(['campaign' => $campaignId, 'id' => $id]);
    }

    public function findOneByXuid($xuid)
    {
        return $this->findOneBy(['xuid' => $xuid]);
    }

    public function findByUser($user)
    {
        return $this->findBy(['user' => $user]);
    }

    public function findByUserAndCampaignId($user, $campaignId)
    {
        return $this->findBy(['user' => $user, 'campaign' => $campaignId]);
    }

    public function findOneByCampaignIdAndCode($campaignId, $code)
    {
        return $this->findOneBy(['campaign' => $campaignId, 'code' => $code]);
    }
}
