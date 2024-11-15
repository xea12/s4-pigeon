<?php
// src/Entity/Campaign.php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\CampaignRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;


#[ORM\Entity(repositoryClass: CampaignRepository::class)]
#[ORM\Table(name: 'kampanie')]
class Campaign
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $nazwa;

    #[ORM\Column(type: 'string', length: 255)]
    private $temat;

    #[ORM\Column(type: 'string', length: 255)]
    private $nazwa_nadawcy;

    #[ORM\Column(type: 'string', length: 100)]
    private $email_nadawcy;

    #[ORM\Column(type: 'datetime')]
    private $data_utworzenia;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private $data_wysylki;

    #[ORM\Column(type: 'string', length: 20)]
    private $status;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $segment;

    #[ORM\Column(type: 'text', nullable: true)]
    private $htmlTemplate;

    #[ORM\OneToMany(mappedBy: 'campaign', targetEntity: CampaignSent::class)]
    private Collection $campaignsSent;

    public function __construct()
    {
        $this->campaignsSent = new ArrayCollection();
    }

    public function getHtmlTemplate(): ?string
    {
        return $this->htmlTemplate;
    }

    public function setHtmlTemplate(?string $htmlTemplate): self
    {
        $this->htmlTemplate = $htmlTemplate;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSegment()
    {
        return $this->segment;
    }

    /**
     * @param mixed $segment
     */
    public function setSegment($segment): void
    {
        $this->segment = $segment;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param mixed $status
     */
    public function setStatus($status): void
    {
        $this->status = $status;
    }

    /**
     * @return mixed
     */
    public function getDataWysylki()
    {
        return $this->data_wysylki;
    }

    /**
     * @param mixed $data_wysylki
     */
    public function setDataWysylki($data_wysylki): void
    {
        $this->data_wysylki = $data_wysylki;
    }

    /**
     * @return mixed
     */
    public function getDataUtworzenia()
    {
        return $this->data_utworzenia;
    }

    /**
     * @param mixed $data_utworzenia
     */
    public function setDataUtworzenia($data_utworzenia): void
    {
        $this->data_utworzenia = $data_utworzenia;
    }

    /**
     * @return mixed
     */
    public function getEmailNadawcy()
    {
        return $this->email_nadawcy;
    }

    /**
     * @param mixed $email_nadawcy
     */
    public function setEmailNadawcy($email_nadawcy): self
    {
        $this->email_nadawcy = $email_nadawcy;
        return $this;
    }

    public function getNazwaNadawcy(): ?string
    {
        return $this->nazwa_nadawcy;
    }

    public function setNazwaNadawcy(string $nazwa_nadawcy): self
    {
        $this->nazwa_nadawcy = $nazwa_nadawcy;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTemat()
    {
        return $this->temat;
    }

    /**
     * @param mixed $temat
     */
    public function setTemat($temat): void
    {
        $this->temat = $temat;
    }

    /**
     * @return mixed
     */
    public function getNazwa()
    {
        return $this->nazwa;
    }

    /**
     * @param mixed $nazwa
     */
    public function setNazwa($nazwa): void
    {
        $this->nazwa = $nazwa;
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

}