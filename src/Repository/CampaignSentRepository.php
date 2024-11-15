<?php

// src/Repository/CampaignSentRepository.php

namespace App\Repository;

use App\Entity\CampaignSent;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class CampaignSentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CampaignSent::class);
    }

    // Możesz dodać tutaj niestandardowe metody zapytań
}