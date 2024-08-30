<?php

namespace App\Entity;

use App\Repository\ReservationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReservationRepository::class)]
class Reservation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $reservationDate = null;

    #[ORM\Column]
    private ?bool $confirmReservation = null;

    #[ORM\ManyToOne(inversedBy: 'reservations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Client $confirmation = null;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getReservationDate(): ?\DateTimeInterface
    {
        return $this->reservationDate;
    }

    public function setReservationDate(\DateTimeInterface $reservationDate): static
    {
        $this->reservationDate = $reservationDate;

        return $this;
    }

    public function isConfirmReservation(): ?bool
    {
        return $this->confirmReservation;
    }

    public function setConfirmReservation(bool $confirmReservation): static
    {
        $this->confirmReservation = $confirmReservation;

        return $this;
    }

    public function getConfirmation(): ?Client
    {
        return $this->confirmation;
    }

    public function setConfirmation(?Client $confirmation): static
    {
        $this->confirmation = $confirmation;

        return $this;
    }
}
