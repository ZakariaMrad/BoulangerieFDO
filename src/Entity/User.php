<?php

namespace App\Entity;

use App\Core\Constants;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "idUser")]
    private ?int $idUser = null;

    #[ORM\Column(length: 180, unique: true, name: 'email')]
    #[Assert\Email(message: "Your email address ( {{ value }} ) is invalid")]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = [];


    #[ORM\Column(name: 'lastName')]
    #[Assert\Length(min: 2, minMessage: "Your last name must contain at least 2 characters")]
    #[Assert\Length(max: 30, maxMessage: "Your last name must contain at most 30 characters")]
    private ?string $lastName = null;

    #[ORM\Column(name: 'firstName')]
    #[Assert\Length(min: 2, minMessage: "Your first name must contain at least 2 characters")]
    #[Assert\Length(max: 30, maxMessage: "Your first name must contain at most 30 characters")]
    private ?string $firstName = null;
    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column]
    #[Assert\Length(min: 2, minMessage: "Your address must contain at least 2 characters")]
    #[Assert\Length(max: 30, maxMessage: "Your address must contain at most 30 characters")]
    private ?string $address = null;

    #[ORM\Column]
    #[Assert\Length(min: 2, minMessage: "Your city's name must contain at least 2 characters")]
    #[Assert\Length(max: 30, maxMessage: "Your city's name must contain at most 30 characters")]
    private ?string $city = null;

    #[ORM\Column]
    #[Assert\Regex(pattern: "/^(?!.*[DFIOQU])[A-VXY]([0-9][A-Z]){2}[0-9]$/", message: "Invalid postal code. Format: A1A 1A1 (letters D-F-I-O-Q-U prohibited and W and Z prohibited in first position)")]
    private ?string $postalcode = null;

    #[ORM\Column]
    private ?string $province = null;

    #[ORM\Column]
    #[Assert\Length(min: 10, minMessage: "Your phone number must contain 10 numbers")]
    #[Assert\Length(max: 10, maxMessage: "Your phone number must contain 10 numbers")]
    private ?string $phone = null;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Order::class)]
    private Collection $orders;

    public function __construct()
    {
        $this->orders = new ArrayCollection();
    }

    public function __set($name, $value)
    {
        switch ($name) {
            case 'lastName':
                $this->lastName = $value;
                return $this;
            case 'firstName':
                $this->firstName = $value;
                return $this;
            case 'phone':
                $this->phone = $value;
                return $this;
            case 'address':
                $this->address = $value;
                return $this;
            case 'city':
                $this->city = $value;
                return $this;
            case 'postalcode':
                $this->postalcode = $value;
                return $this;
            case 'province':
                $this->province = $value;
                return $this;
            default:
                return null;
        }
    }
    public function getIdUser(): ?int
    {
        return $this->idUser;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }
    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }
    public function getLastName(): string
    {
        return $this->lastName;
    }
    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }


    public function getFirstName(): string
    {
        return $this->firstName;
    }
    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }
    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }
    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getAddress(): string
    {
        return $this->address;
    }
    public function setAddress(string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getCity(): string
    {
        return $this->city;
    }
    public function setCity(string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getPostalcode(): string
    {
        return $this->postalcode;
    }
    public function setPostalcode(string $postalcode): self
    {
        $this->postalcode = $postalcode;

        return $this;
    }

    public function getProvince(): string
    {
        return $this->province;
    }
    public function setProvince(string $province): self
    {
        $this->province = $province;

        return $this;
    }

    public function getPhone(): string
    {
        return $this->phone;
    }
    public function setPhone(string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }


    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getCurrentOrders()
    {
        $orders = $this->getOrders();
        $currentOrders = [];
        foreach ($orders as $order) {
            if ($order->getState() != Constants::orderState['delivered'])
                $currentOrders[] = $order;
        }
        return $currentOrders;
    }
    public function getPastOrders()
    {
        $orders = $this->getOrders();
        $pastOrders = [];
        foreach ($orders as $order) {
            if ($order->getState() == Constants::orderState['delivered'])
                $pastOrders[] = $order;
        }
        return $pastOrders;
    }

    /**
     * @return Collection<int, Order>
     */
    public function getOrders(): Collection
    {
        return $this->orders;
    }

    public function addOrder(Order $order): self
    {
        if (!$this->orders->contains($order)) {
            $this->orders->add($order);
            $order->setUser($this);
        }

        return $this;
    }

    public function removeOrder(Order $order): self
    {
        if ($this->orders->removeElement($order)) {
            // set the owning side to null (unless already changed)
            if ($order->getUser() === $this) {
                $order->setUser(null);
            }
        }

        return $this;
    }
}
