<?php

namespace App\Entity;

use App\Repository\CustomerRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity(repositoryClass: CustomerRepository::class)]
#[ORM\Table(name: 'klienci')]
class Customer
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 1000)]
    private $email;

    #[ORM\Column(type: 'string', length: 1000, nullable: true)]
    private $imie;

    #[ORM\Column(type: 'string', length: 1000, nullable: true)]
    private $zakup;

    #[ORM\Column(type: 'string', length: 1000, nullable: true)]
    private $product_id;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $product_nazwa;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $product_obrazek;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $product_cena;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $product_cenalow;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $product_typ;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $emhash;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $rabat;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $discount;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $customers_days_from_order;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $customers_orders_count;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $customers_balance;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $customers_firma;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $customers_value_class;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $customers_time_class;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $printer_id;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $printer_name;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $printer_image;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $city;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $last_review;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $subject;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $subject_type;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $nazwa_short;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $liczba;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $producent;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $technologia;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $lokalny;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $preheader;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $b2b_b2c;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $product_rodzaj_nazwa;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $product_typ_nazwa;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $odroczony;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $publiczny;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $przedszkole;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $segment_product_type;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $segment_otwierane;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $segment_firma;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $segment_czas;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $segment;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $AB_test;

    #[ORM\Column(type: 'date')]
    private $add_date;

    #[ORM\OneToMany(mappedBy: 'customer', targetEntity: CampaignSent::class)]
    private Collection $campaignsSent;

    public function __construct()
    {
        $this->campaignsSent = new ArrayCollection();
    }

    /**
     * @return Collection<int, CampaignSent>
     */
    public function getCampaignsSent(): Collection
    {
        return $this->campaignsSent;
    }

    public function addCampaignSent(CampaignSent $campaignSent): self
    {
        if (!$this->campaignsSent->contains($campaignSent)) {
            $this->campaignsSent->add($campaignSent);
            $campaignSent->setCustomer($this);
        }

        return $this;
    }

    public function removeCampaignSent(CampaignSent $campaignSent): self
    {
        if ($this->campaignsSent->removeElement($campaignSent)) {
            // set the owning side to null (unless already changed)
            if ($campaignSent->getCustomer() === $this) {
                $campaignSent->setCustomer(null);
            }
        }

        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function getImie(): ?string
    {
        return $this->imie;
    }

    public function setImie(?string $imie): self
    {
        $this->imie = $imie;
        return $this;
    }

    public function getZakup(): ?string
    {
        return $this->zakup;
    }

    public function setZakup(?string $zakup): self
    {
        $this->zakup = $zakup;
        return $this;
    }

    public function getProductId(): ?string
    {
        return $this->product_id;
    }

    public function setProductId(?string $product_id): self
    {
        $this->product_id = $product_id;
        return $this;
    }

    public function getProductNazwa(): ?string
    {
        return $this->product_nazwa;
    }

    public function setProductNazwa(?string $product_nazwa): self
    {
        $this->product_nazwa = $product_nazwa;
        return $this;
    }

    public function getProductObrazek(): ?string
    {
        return $this->product_obrazek;
    }

    public function setProductObrazek(?string $product_obrazek): self
    {
        $this->product_obrazek = $product_obrazek;
        return $this;
    }

    public function getProductCena(): ?string
    {
        return $this->product_cena;
    }

    public function setProductCena(?string $product_cena): self
    {
        $this->product_cena = $product_cena;
        return $this;
    }

    public function getProductCenalow(): ?string
    {
        return $this->product_cenalow;
    }

    public function setProductCenalow(?string $product_cenalow): self
    {
        $this->product_cenalow = $product_cenalow;
        return $this;
    }

    public function getProductTyp(): ?string
    {
        return $this->product_typ;
    }

    public function setProductTyp(?string $product_typ): self
    {
        $this->product_typ = $product_typ;
        return $this;
    }

    public function getEmhash(): ?string
    {
        return $this->emhash;
    }

    public function setEmhash(?string $emhash): self
    {
        $this->emhash = $emhash;
        return $this;
    }

    public function getRabat(): ?string
    {
        return $this->rabat;
    }

    public function setRabat(?string $rabat): self
    {
        $this->rabat = $rabat;
        return $this;
    }

    public function getDiscount(): ?string
    {
        return $this->discount;
    }

    public function setDiscount(?string $discount): self
    {
        $this->discount = $discount;
        return $this;
    }

    public function getCustomersDaysFromOrder(): ?string
    {
        return $this->customers_days_from_order;
    }

    public function setCustomersDaysFromOrder(?string $customers_days_from_order): self
    {
        $this->customers_days_from_order = $customers_days_from_order;
        return $this;
    }

    public function getCustomersOrdersCount(): ?string
    {
        return $this->customers_orders_count;
    }

    public function setCustomersOrdersCount(?string $customers_orders_count): self
    {
        $this->customers_orders_count = $customers_orders_count;
        return $this;
    }

    public function getCustomersBalance(): ?string
    {
        return $this->customers_balance;
    }

    public function setCustomersBalance(?string $customers_balance): self
    {
        $this->customers_balance = $customers_balance;
        return $this;
    }

    public function getCustomersFirma(): ?string
    {
        return $this->customers_firma;
    }

    public function setCustomersFirma(?string $customers_firma): self
    {
        $this->customers_firma = $customers_firma;
        return $this;
    }

    public function getCustomersValueClass(): ?string
    {
        return $this->customers_value_class;
    }

    public function setCustomersValueClass(?string $customers_value_class): self
    {
        $this->customers_value_class = $customers_value_class;
        return $this;
    }

    public function getCustomersTimeClass(): ?string
    {
        return $this->customers_time_class;
    }

    public function setCustomersTimeClass(?string $customers_time_class): self
    {
        $this->customers_time_class = $customers_time_class;
        return $this;
    }

    public function getPrinterId(): ?string
    {
        return $this->printer_id;
    }

    public function setPrinterId(?string $printer_id): self
    {
        $this->printer_id = $printer_id;
        return $this;
    }

    public function getPrinterName(): ?string
    {
        return $this->printer_name;
    }

    public function setPrinterName(?string $printer_name): self
    {
        $this->printer_name = $printer_name;
        return $this;
    }

    public function getPrinterImage(): ?string
    {
        return $this->printer_image;
    }

    public function setPrinterImage(?string $printer_image): self
    {
        $this->printer_image = $printer_image;
        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): self
    {
        $this->city = $city;
        return $this;
    }

    public function getLastReview(): ?string
    {
        return $this->last_review;
    }

    public function setLastReview(?string $last_review): self
    {
        $this->last_review = $last_review;
        return $this;
    }

    public function getSubject(): ?string
    {
        return $this->subject;
    }

    public function setSubject(?string $subject): self
    {
        $this->subject = $subject;
        return $this;
    }

    public function getSubjectType(): ?string
    {
        return $this->subject_type;
    }

    public function setSubjectType(?string $subject_type): self
    {
        $this->subject_type = $subject_type;
        return $this;
    }

    public function getNazwaShort(): ?string
    {
        return $this->nazwa_short;
    }

    public function setNazwaShort(?string $nazwa_short): self
    {
        $this->nazwa_short = $nazwa_short;
        return $this;
    }

    public function getLiczba(): ?string
    {
        return $this->liczba;
    }

    public function setLiczba(?string $liczba): self
    {
        $this->liczba = $liczba;
        return $this;
    }

    public function getProducent(): ?string
    {
        return $this->producent;
    }

    public function setProducent(?string $producent): self
    {
        $this->producent = $producent;
        return $this;
    }

    public function getTechnologia(): ?string
    {
        return $this->technologia;
    }

    public function setTechnologia(?string $technologia): self
    {
        $this->technologia = $technologia;
        return $this;
    }

    public function getLokalny(): ?string
    {
        return $this->lokalny;
    }

    public function setLokalny(?string $lokalny): self
    {
        $this->lokalny = $lokalny;
        return $this;
    }

    public function getPreheader(): ?string
    {
        return $this->preheader;
    }

    public function setPreheader(?string $preheader): self
    {
        $this->preheader = $preheader;
        return $this;
    }

    public function getB2bB2c(): ?string
    {
        return $this->b2b_b2c;
    }

    public function setB2bB2c(?string $b2b_b2c): self
    {
        $this->b2b_b2c = $b2b_b2c;
        return $this;
    }

    public function getProductRodzajNazwa(): ?string
    {
        return $this->product_rodzaj_nazwa;
    }

    public function setProductRodzajNazwa(?string $product_rodzaj_nazwa): self
    {
        $this->product_rodzaj_nazwa = $product_rodzaj_nazwa;
        return $this;
    }

    public function getProductTypNazwa(): ?string
    {
        return $this->product_typ_nazwa;
    }

    public function setProductTypNazwa(?string $product_typ_nazwa): self
    {
        $this->product_typ_nazwa = $product_typ_nazwa;
        return $this;
    }

    public function getOdroczony(): ?string
    {
        return $this->odroczony;
    }

    public function setOdroczony(?string $odroczony): self
    {
        $this->odroczony = $odroczony;
        return $this;
    }

    public function getPubliczny(): ?string
    {
        return $this->publiczny;
    }

    public function setPubliczny(?string $publiczny): self
    {
        $this->publiczny = $publiczny;
        return $this;
    }

    public function getPrzedszkole(): ?string
    {
        return $this->przedszkole;
    }

    public function setPrzedszkole(?string $przedszkole): self
    {
        $this->przedszkole = $przedszkole;
        return $this;
    }

    public function getSegmentProductType(): ?string
    {
        return $this->segment_product_type;
    }

    public function setSegmentProductType(?string $segment_product_type): self
    {
        $this->segment_product_type = $segment_product_type;
        return $this;
    }

    public function getSegmentOtwierane(): ?string
    {
        return $this->segment_otwierane;
    }

    public function setSegmentOtwierane(?string $segment_otwierane): self
    {
        $this->segment_otwierane = $segment_otwierane;
        return $this;
    }

    public function getSegmentFirma(): ?string
    {
        return $this->segment_firma;
    }

    public function setSegmentFirma(?string $segment_firma): self
    {
        $this->segment_firma = $segment_firma;
        return $this;
    }

    public function getSegmentCzas(): ?string
    {
        return $this->segment_czas;
    }

    public function setSegmentCzas(?string $segment_czas): self
    {
        $this->segment_czas = $segment_czas;
        return $this;
    }

    public function getSegment(): ?string
    {
        return $this->segment;
    }

    public function setSegment(?string $segment): self
    {
        $this->segment = $segment;
        return $this;
    }

    public function getABTest(): ?string
    {
        return $this->AB_test;
    }

    public function setABTest(?string $AB_test): self
    {
        $this->AB_test = $AB_test;
        return $this;
    }

    public function getAddDate(): ?\DateTimeInterface
    {
        return $this->add_date;
    }

    public function setAddDate(\DateTimeInterface $add_date): self
    {
        $this->add_date = $add_date;
        return $this;
    }
}