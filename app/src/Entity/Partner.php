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
}
