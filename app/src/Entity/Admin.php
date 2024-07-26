<?php

namespace App\Entity;

use App\Repository\AdminRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Entity
 */
#[ORM\Entity]
class Admin extends User
{
    /**
     * @var Collection<int, Client>
     */
    #[ORM\OneToMany(targetEntity: Client::class, mappedBy: 'admin')]
    private $clients;

    /**
     * @var Collection<int, Partner>
     */
    #[ORM\OneToMany(targetEntity: Partner::class, mappedBy: 'admin')]
    private $partners;

    /**
     * @var Admin|null
     */
    #[ORM\ManyToOne(targetEntity: self::class, inversedBy: 'subAdmins')]
    private $managedBy = null;

    /**
     * @var Collection<int, self>
     */
    #[ORM\OneToMany(targetEntity: self::class, mappedBy: 'managedBy')]
    private $subAdmins;

    /**
     * @var Collection<int, self>
     */
    #[ORM\OneToMany(targetEntity: self::class, mappedBy: 'managedBy')]
    private $managedAdmins;

    public function __construct($email, $password, $role, $firstName, $lastName, $phoneNumber)
    {
        parent::__construct($email, $password, $role, $firstName, $lastName, $phoneNumber);
        $this->clients = new ArrayCollection();
        $this->partners = new ArrayCollection();
        $this->subAdmins = new ArrayCollection();
        $this->managedAdmins = new ArrayCollection();
    }

    /**
     * @return Collection<int, Client>
     */
    public function getClients(): Collection
    {
        return $this->clients;
    }

    public function addClient(Client $client): self
    {
        if (!$this->clients->contains($client)) {
            $this->clients->add($client);
            $client->setAdmin($this);
        }

        return $this;
    }

    public function removeClient(Client $client): self
    {
        if ($this->clients->removeElement($client)) {
            if ($client->getAdmin() === $this) {
                $client->setAdmin(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Partner>
     */
    public function getPartners(): Collection
    {
        return $this->partners;
    }

    public function addPartner(Partner $partner): self
    {
        if (!$this->partners->contains($partner)) {
            $this->partners->add($partner);
            $partner->setAdmin($this);
        }

        return $this;
    }

    public function removePartner(Partner $partner): self
    {
        if ($this->partners->removeElement($partner)) {
            if ($partner->getAdmin() === $this) {
                $partner->setAdmin(null);
            }
        }

        return $this;
    }

    public function getManagedBy(): ?self
    {
        return $this->managedBy;
    }

    public function setManagedBy(?self $managedBy): self
    {
        $this->managedBy = $managedBy;

        return $this;
    }

    /**
     * @return Collection<int, self>
     */
    public function getSubAdmins(): Collection
    {
        return $this->subAdmins;
    }

    public function addSubAdmin(self $subAdmin): self
    {
        if (!$this->subAdmins->contains($subAdmin)) {
            $this->subAdmins->add($subAdmin);
            $subAdmin->setManagedBy($this);
        }

        return $this;
    }

    public function removeSubAdmin(self $subAdmin): self
    {
        if ($this->subAdmins->removeElement($subAdmin)) {
            if ($subAdmin->getManagedBy() === $this) {
                $subAdmin->setManagedBy(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, self>
     */
    public function getManagedAdmins(): Collection
    {
        return $this->managedAdmins;
    }

    public function addManagedAdmin(self $managedAdmin): self
    {
        if (!$this->managedAdmins->contains($managedAdmin)) {
            $this->managedAdmins->add($managedAdmin);
            $managedAdmin->setManagedBy($this);
        }

        return $this;
    }

    public function removeManagedAdmin(self $managedAdmin): self
    {
        if ($this->managedAdmins->removeElement($managedAdmin)) {
            if ($managedAdmin->getManagedBy() === $this) {
                $managedAdmin->setManagedBy(null);
            }
        }

        return $this;
    }
}
