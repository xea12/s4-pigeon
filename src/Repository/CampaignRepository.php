<?php

namespace App\Repository;

use App\Entity\Campaign;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class CampaignRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Campaign::class);
    }
    public function getUniqueSegments(): array
    {
        return $this->createQueryBuilder('c')
            ->select('DISTINCT c.segment')
            ->where('c.segment IS NOT NULL')
            ->getQuery()
            ->getSingleColumnResult();
    }



    public function findCustomersForCampaign(Campaign $campaign): array
    {
        return $this->getEntityManager()
            ->createQueryBuilder()
            ->select('c')
            ->from('App\Entity\Customer', 'c')
            ->where('c.segment = :segment')
            ->setParameter('segment', $campaign->getSegment())
            ->getQuery()
            ->getResult();
    }

    public function getOpenRateForCampaign(Campaign $campaign): float
    {
        $totalSent = $this->createQueryBuilder('c')
            ->select('COUNT(cs.id)')
            ->join('c.campaignsSent', 'cs')
            ->where('c = :campaign')
            ->setParameter('campaign', $campaign)
            ->getQuery()
            ->getSingleScalarResult();

        $totalOpened = $this->createQueryBuilder('c')
            ->select('COUNT(DISTINCT co.id)')
            ->join('c.campaignOpens', 'co')
            ->where('c = :campaign')
            ->setParameter('campaign', $campaign)
            ->getQuery()
            ->getSingleScalarResult();

        return $totalSent > 0 ? ($totalOpened / $totalSent) * 100 : 0;
    }

}