<?php

// src/Entity/CampaignOpen.php

namespace App\Entity;

use App\Repository\CampaignOpenRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CampaignOpenRepository::class)]
class CampaignOpen
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\ManyToOne(targetEntity: Campaign::class)]
    #[ORM\JoinColumn(nullable: false)]
    private $campaign;

    #[ORM\ManyToOne(targetEntity: Customer::class)]
    #[ORM\JoinColumn(nullable: false)]
    private $customer;

    #[ORM\Column(type: 'datetime')]
    private $openedAt;

    #[ORM\Column(type: 'string', length: 45, nullable: true)]
    private $ipAddress;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $userAgent;

    #[ORM\ManyToOne(targetEntity: CampaignSent::class, inversedBy: 'campaignOpens')]
    #[ORM\JoinColumn(nullable: false)]
    private $campaignSent;

    public function getCampaignSent(): ?CampaignSent
    {
        return $this->campaignSent;
    }

    public function setCampaignSent(?CampaignSent $campaignSent): self
    {
        $this->campaignSent = $campaignSent;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getUserAgent()
    {
        return $this->userAgent;
    }

    /**
     * @param mixed $userAgent
     */
    public function setUserAgent($userAgent): void
    {
        $this->userAgent = $userAgent;
    }

    /**
     * @return mixed
     */
    public function getIpAddress()
    {
        return $this->ipAddress;
    }

    /**
     * @param mixed $ipAddress
     */
    public function setIpAddress($ipAddress): void
    {
        $this->ipAddress = $ipAddress;
    }



    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getOpenedAt()
    {
        return $this->openedAt;
    }

    /**
     * @param mixed $openedAt
     */
    public function setOpenedAt($openedAt): void
    {
        $this->openedAt = $openedAt;
    }

    /**
     * @return mixed
     */
    public function getCustomer()
    {
        return $this->customer;
    }

    /**
     * @param mixed $customer
     */
    public function setCustomer($customer): void
    {
        $this->customer = $customer;
    }

    /**
     * @return mixed
     */
    public function getCampaign()
    {
        return $this->campaign;
    }

    /**
     * @param mixed $campaign
     */
    public function setCampaign($campaign): void
    {
        $this->campaign = $campaign;
    }


}