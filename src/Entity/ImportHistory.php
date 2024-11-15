<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'import_history')]
class ImportHistory
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'datetime')]
    private $importDate;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $email;

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
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email): void
    {
        $this->email = $email;
    }

    /**
     * @return mixed
     */
    public function getImportDate(): ?\DateTimeInterface
    {
        return $this->importDate;
    }

    /**
     * @param mixed $importDate
     */
    public function setImportDate(\DateTimeInterface $importDate): self
    {
        $this->importDate = $importDate;
        return $this;
    }

}