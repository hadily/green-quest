<?php

namespace App\Entity;

use App\Entity\Admin;
use App\Repository\ClientRepository;
use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Entity
 */
#[ORM\Entity]
class Client extends User
{
    #[ORM\ManyToOne(targetEntity: Admin::class, inversedBy: 'clients')]
    #[ORM\JoinColumn(nullable: false)]
    private $admin = null;


    public function getAdmin(): ?Admin
    {
        return $this->admin;
    }

    public function setAdmin(?Admin $admin): Admin
    {
        $this->admin = $admin;

        return $this;
    }
    
}
?>