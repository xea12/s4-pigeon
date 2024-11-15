<?php

// src/Entity/CampaignSent.php

namespace App\Entity;

use App\Repository\CampaignSentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CampaignSentRepository::class)]
class CampaignSent
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Campaign::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Campaign $campaign = null;

    #[ORM\ManyToOne(targetEntity: Customer::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Customer $customer = null;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $sentAt = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $status = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $trackingId;

    #[ORM\OneToMany(targetEntity: CampaignOpen::class, mappedBy: 'campaignSent')]
    private Collection $campaignOpens;

    public function __construct()
    {
        $this->campaignOpens = new ArrayCollection();
    }

    public function getCampaignOpens(): Collection
    {
        return $this->campaignOpens;
    }

    public function addCampaignOpen(CampaignOpen $campaignOpen): self
    {
        if (!$this->campaignOpens->contains($campaignOpen)) {
            $this->campaignOpens[] = $campaignOpen;
            $campaignOpen->setCampaignSent($this);
        }

        return $this;
    }

    public function removeCampaignOpen(CampaignOpen $campaignOpen): self
    {
        if ($this->campaignOpens->removeElement($campaignOpen)) {
            // set the owning side to null (unless already changed)
            if ($campaignOpen->getCampaignSent() === $this) {
                $campaignOpen->setCampaignSent(null);
            }
        }

        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): void
    {
        $this->status = $status;
    }

    public function getSentAt(): ?\DateTimeInterface
    {
        return $this->sentAt;
    }

    public function setSentAt(?\DateTimeInterface $sentAt): void
    {
        $this->sentAt = $sentAt;
    }

    public function getCustomer(): ?Customer
    {
        return $this->customer;
    }

    public function setCustomer(?Customer $customer): void
    {
        $this->customer = $customer;
    }

    public function getCampaign(): ?Campaign
    {
        return $this->campaign;
    }

    public function setCampaign(?Campaign $campaign): void
    {
        $this->campaign = $campaign;
    }

    public function getTrackingId(): ?string
    {
        return $this->trackingId;
    }

    public function setTrackingId(?string $trackingId): self
    {
        $this->trackingId = $trackingId;
        return $this;
    }

    // Getters and setters...
}
