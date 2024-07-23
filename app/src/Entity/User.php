<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @ORM\MappedSuperclass
 */
abstract class User
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    protected $email;

    /**
     * @ORM\Column(type="string")
     */
    protected $password;

    /**
     * @ORM\Column(type="string")
     */
    protected $role;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $firstName;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $lastName;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $phoneNumber;

    public function __construct($email, $password, $role, $firstName = null, $lastName = null, $phoneNumber = null) {
        $this->email = $email;
        $this->password = $password;
        $this->role = $role;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->phoneNumber = $phoneNumber;
    }

    // Getters and setters

    public function getId(): int {
        return $this->id;
    }

    public function getEmail(): string {
        return $this->email;
    }

    public function setEmail(string $email): self {
        $this->email = $email;
        return $this;
    }

    public function getPassword(): string {
        return $this->password;
    }

    public function setPassword(string $password): self {
        $this->password = $password;
        return $this;
    }

    public function getRole(): string {
        return $this->role;
    }

    public function setRole(string $role): self {
        $this->role = $role;
        return $this;
    }

    public function getFirstName(): string {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self {
        $this->firstName = $firstName;
        return $this;
    }

    public function getLastName(): string {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self {
        $this->lastName = $lastName;
        return $this;
    }

    public function getPhoneNumber(): string {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(?string $phoneNumber): self {
        $this->phoneNumber = $phoneNumber;
        return $this;
    }

    // Methods signup, login, update profile

    public function signup(UserPasswordEncoderInterface $passwordEncoder, $plainPassword): self {
        $encodedPassword = $passwordEncoder->encodePassword($this, $plainPassword);
        $this->setPassword($encodedPassword);
        return $this;
    }

    public function login($email, $plainPassword, UserPasswordEncoderInterface $passwordEncoder): bool {
        if ($this->getEmail() === $email && $passwordEncoder->isPasswordValid($this, $plainPassword)) {
            return true;
        }
        return false;
    }

    public function updateProfile($firstName, $lastName, $phoneNumber): self {
        if ($firstName !== null) {
            $this->setFirstName($firstName);
        }
        if ($lastName !== null) {
            $this->setLastName($lastName);
        }
        if ($phoneNumber !== null) {
            $this->setPhoneNumber($phoneNumber);
        }
        return $this;
    }
}
?>
