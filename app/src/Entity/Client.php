<?php

namespace App\Entity;

use App\Repository\ClientRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ClientRepository::class)]
class Client extends User
{
    public function __contruct()
    {
        parent::__construct();
        $this->setRoles(['CLIENT']);
    }

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $localisation = null;

    public function getLocalisation(): ?string
    {
        return $this->localisation;
    }

    public function setLocalisation(?string $localisation): void
    {
        $this->localisation = $localisation;
    }
}