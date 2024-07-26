<?php

namespace App\Entity;

use App\Entity\Admin;
use App\Repository\PartnerRepository;
use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Entity
 */
#[ORM\Entity]
class Partner extends User
{
    /**
     * @var Collection<int, Partner>
     */
    #[ORM\ManyToOne(targetEntity: Admin::class, inversedBy: 'partners')]
    #[ORM\JoinColumn(nullable: false)]
    private $admin;
    
    public function getAdmin(): ?Admin
    {
        return $this->admin;
    }

    public function setAdmin(Admin $admin): Admin
    {
        $this->admin = $admin;

        return $this;
    }
    
}
?>
