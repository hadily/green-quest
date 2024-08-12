<?php

namespace App\Entity;

use App\Repository\AdminRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AdminRepository::class)]
class Admin extends User
{
    /**
     * @var Collection<int, Partner>
     */
    #[ORM\OneToMany(targetEntity: Partner::class, mappedBy: 'admin')]
    private Collection $partners;

    /**
     * @var Collection<int, Client>
     */
    #[ORM\OneToMany(targetEntity: Client::class, mappedBy: 'admin')]
    private Collection $clients;

    /**
     * @var Collection<int, Complaints>
     */
    #[ORM\OneToMany(targetEntity: Complaints::class, mappedBy: 'admin')]
    private Collection $fixedComplaints;

    public function __construct()
    {
        $this->partners = new ArrayCollection();
        $this->clients = new ArrayCollection();
        $this->fixedComplaints = new ArrayCollection();
    }

    public function __contruct()
    {
        parent::__construct();
        $this->setRoles(['ADMIN']);
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
     * @return Collection<int, Complaints>
     */
    public function getFixedComplaints(): Collection
    {
        return $this->fixedComplaints;
    }

    public function addFixedComplaint(Complaints $complaint): static
    {
        if (!$this->fixedComplaints->contains($complaint)) {
            $this->fixedComplaints->add($complaint);
            $complaint->setAdmin($this);
        }

        return $this;
    }

    public function removeFixedComplaint(Complaints $complaint): static
    {
        if ($this->fixedComplaints->removeElement($complaint)) {
            // set the owning side to null (unless already changed)
            if ($complaint->getAdmin() === $this) {
                $complaint->setAdmin(null);
            }
        }

        return $this;
    }
}
