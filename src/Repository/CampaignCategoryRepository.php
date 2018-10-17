<?php

namespace App\Repository;

use App\Entity\CampaignCategory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method CampaignCategory|null find($id, $lockMode = null, $lockVersion = null)
 * @method CampaignCategory|null findOneBy(array $criteria, array $orderBy = null)
 * @method CampaignCategory[]    findAll()
 * @method CampaignCategory[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CampaignCategoryRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, CampaignCategory::class);
    }

//    /**
//     * @return CampaignCategory[] Returns an array of CampaignCategory objects
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
    public function findOneBySomeField($value): ?CampaignCategory
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    public function findOneByCampaignIdAndCategoryId($campaignId, $categoryId)
    {
        return $this->findOneBy(['campaign' => $campaignId, 'category' => $categoryId]);
    }
}
