<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: 'user', uniqueConstraints: [ new ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_ID', fields: ['id']) ])]
#[ORM\InheritanceType("SINGLE_TABLE")]
#[ORM\DiscriminatorColumn(name: "discriminator", type: "string")]
#[ORM\DiscriminatorMap(["user" => User::class, "admin" => Admin::class, "partner" => Partner::class, "client" => Client::class])]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column(type: 'json')]
    private $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column(type: 'string')]
    private $password = null;

    #[ORM\Column(type:'string', length: 255, nullable: true)]
    private $name = null;

    #[ORM\Column(type:'string', length: 255, nullable: true)]
    private $lastName = null;

    #[ORM\Column(type:'string', length: 255, nullable: true)]
    private $phoneNuumber = null;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function setId(string $id): int
    {
        $this->id = $id;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->id;
    }

    /**
     * @see UserInterface
     *
     * @return list<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): array
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): array
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): string
    {
        $this->name = $name;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(?string $lastName): string
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getPhoneNuumber(): ?string
    {
        return $this->phoneNuumber;
    }

    public function setPhoneNuumber(?string $phoneNuumber): string
    {
        $this->phoneNuumber = $phoneNuumber;

        return $this;
    }
}
