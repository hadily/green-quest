<?php

namespace App\Entity;

use App\Repository\ClientRepository;
use Doctrine\ORM\Mapping as ORM;

// [ORM\Entity(repositoryClass: ClientRepository::class)]
class Client extends User
{
    #[ORM\ManyToOne(inversedBy: 'clients')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Admin $admin = null;

    //[ORM\Id]
    //[ORM\GeneratedValue]
    //[ORM\Column]

    public function getAdmin(): ?Admin
    {
        return $this->admin;
    }

    public function setAdmin(?Admin $admin): static
    {
        $this->admin = $admin;

        return $this;
    }
    
}
?>