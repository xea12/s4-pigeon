<?php

// src/Repository/CampaignOpenRepository.php
namespace App\Repository;

use App\Entity\Campaign;
use App\Entity\CampaignOpen;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class CampaignOpenRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CampaignOpen::class);
    }

    public function getOpenStatistics(Campaign $campaign): array
    {
        return $this->createQueryBuilder('co')
            ->select('cs.customer as customer')
            ->addSelect('MIN(co.openedAt) as firstOpen')
            ->addSelect('MAX(co.openedAt) as lastOpen')
            ->addSelect('COUNT(co.id) as openCount')
            ->join('co.campaignSent', 'cs')
            ->where('cs.campaign = :campaign')
            ->groupBy('cs.customer')
            ->setParameter('campaign', $campaign)
            ->getQuery()
            ->getResult();
    }

    public function findOpensByCampaign(Campaign $campaign): array
    {
        return $this->createQueryBuilder('co')
            ->join('co.campaignSent', 'cs')
            ->where('cs.campaign = :campaign')
            ->setParameter('campaign', $campaign)
            ->getQuery()
            ->getResult();
    }

    public function countUniqueOpensByCampaign(Campaign $campaign): int
    {
        return $this->createQueryBuilder('co')
            ->select('COUNT(DISTINCT cs.customer)')
            ->join('co.campaignSent', 'cs')
            ->where('cs.campaign = :campaign')
            ->setParameter('campaign', $campaign)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function save(CampaignOpen $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(CampaignOpen $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}