<?php

namespace App\Entity;

use App\Repository\PartnerRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PartnerRepository::class)]
class Partner extends User
{
    #[ORM\Column(type: 'string', length: 255)]
    private $companyName = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $companyDescription = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $localisation = null;

    #[ORM\ManyToOne(targetEntity: Admin::class, inversedBy: 'partners')]
    #[ORM\JoinColumn(nullable: false)]
    private $admin = null;

    public function __contruct()
    {
        parent::__construct();
        $this->setRoles(['PARTNER']);
    }

    public function getCompanyName(): ?string
    {
        return $this->companyName;
    }

    public function setCompanyName(string $companyName): void
    {
        $this->companyName = $companyName;
    }

    public function getCompanyDescription(): ?string
    {
        return $this->companyDescription;
    }

    public function setCompanyDescription(?string $companyDescription): void
    {
        $this->companyDescription = $companyDescription;
    }

    public function getLocalisation(): ?string
    {
        return $this->localisation;
    }

    public function setLocalisation(?string $localisation): void
    {
        $this->localisation = $localisation;
    }

    public function getAdmin(): Admin
    {
        return $this->admin;
    }

    public function setAdmin(?Admin $admin): self
    {
        $this->admin = $admin;

        return $this;
    }
}
