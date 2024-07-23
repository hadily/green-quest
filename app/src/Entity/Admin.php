<?php

namespace App\Entity;

use App\Repository\AdminRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

// [ORM\Entity(repositoryClass: AdminRepository::class)]
class Admin extends User
{
    /**
     * @var Collection<int, Client>
     */
    #[ORM\OneToMany(targetEntity: Client::class, mappedBy: 'admin')]
    private Collection $clients;

    /**
     * @var Collection<int, Partner>
     */
    #[ORM\OneToMany(targetEntity: Partner::class, mappedBy: 'admin')]
    private Collection $partners;

    #[ORM\ManyToOne(targetEntity: self::class, inversedBy: 'subAdmins')]
    private ?self $managedBy = null;

    /**
     * @var Collection<int, self>
     */
    #[ORM\OneToMany(targetEntity: self::class, mappedBy: 'managedBy')]
    private Collection $subAdmins;

    /**
     * @var Collection<int, self>
     */
    #[ORM\OneToMany(targetEntity: self::class, mappedBy: 'managedBy')]
    private Collection $managedAdmins;

    public function __construct()
    {
        parent::__construct();
        $this->clients = new ArrayCollection();
        $this->partners = new ArrayCollection();
        $this->subAdmins = new ArrayCollection();
        $this->managedAdmins = new ArrayCollection();
    }

    //[ORM\Id]
    //[ORM\GeneratedValue]
    //[ORM\Column]

    /**
     * @return Collection<int, Client>
     */
    public function getClients(): Collection
    {
        return $this->clients;
    }

    public function addClient(Client $client): static
    {
        if (!$this->clients->contains($client)) {
            $this->clients->add($client);
            $client->setAdmin($this);
        }

        return $this;
    }

    public function removeClient(Client $client): static
    {
        if ($this->clients->removeElement($client)) {
            // set the owning side to null (unless already changed)
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

    public function addPartner(Partner $partner): static
    {
        if (!$this->partners->contains($partner)) {
            $this->partners->add($partner);
            $partner->setAdmin($this);
        }

        return $this;
    }

    public function removePartner(Partner $partner): static
    {
        if ($this->partners->removeElement($partner)) {
            // set the owning side to null (unless already changed)
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

    public function setManagedBy(?self $managedBy): static
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

    public function addSubAdmin(self $subAdmin): static
    {
        if (!$this->subAdmins->contains($subAdmin)) {
            $this->subAdmins->add($subAdmin);
            $subAdmin->setManagedBy($this);
        }

        return $this;
    }

    public function removeSubAdmin(self $subAdmin): static
    {
        if ($this->subAdmins->removeElement($subAdmin)) {
            // set the owning side to null (unless already changed)
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

    public function addManagedAdmin(self $managedAdmin): static
    {
        if (!$this->managedAdmins->contains($managedAdmin)) {
            $this->managedAdmins->add($managedAdmin);
            $managedAdmin->setManagedBy($this);
        }

        return $this;
    }

    public function removeManagedAdmin(self $managedAdmin): static
    {
        if ($this->managedAdmins->removeElement($managedAdmin)) {
            // set the owning side to null (unless already changed)
            if ($managedAdmin->getManagedBy() === $this) {
                $managedAdmin->setManagedBy(null);
            }
        }

        return $this;
    }
}

?>
