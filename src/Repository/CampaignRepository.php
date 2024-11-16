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
        $qb = $this->createQueryBuilder('c')
            ->select('COUNT(DISTINCT co.id) as opens, COUNT(DISTINCT cs.id) as sends')
            ->leftJoin('c.campaignsSent', 'cs')
            ->leftJoin('cs.campaignOpens', 'co')
            ->where('c.id = :campaignId')
            ->setParameter('campaignId', $campaign->getId());

        $result = $qb->getQuery()->getSingleResult();

        if ($result['sends'] == 0) {
            return 0;
        }

        return ($result['opens'] / $result['sends']) * 100;
    }
    public function getOpensForCampaign(Campaign $campaign): array
    {
        return $this->createQueryBuilder('c')
            ->select('co')
            ->join('c.campaignsSent', 'cs')
            ->join('cs.campaignOpens', 'co')
            ->where('c = :campaign')
            ->setParameter('campaign', $campaign)
            ->orderBy('co.openedAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

}