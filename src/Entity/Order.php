<?php

namespace App\Entity;

use App\Core\Constants;
use App\Entity\Cart;
use App\Repository\OrderRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OrderRepository::class)]
#[ORM\Table(name: '`order`')]
class Order
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name:'idOrder')]
    private ?int $idOrder = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, name:'dateOrder')]
    private ?\DateTimeInterface $dateOrder = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true, name:'dateDelivery')]
    private ?\DateTimeInterface $dateDelivery = null;

    #[ORM\Column(name:'rateTPS')]
    private ?float $rateTPS = null;

    #[ORM\Column(name:'rateTVQ')]
    private ?float $rateTVQ = null;

    #[ORM\Column(name:'shippingCost')]
    private ?float $shippingCost = null;

    #[ORM\Column(length: 20)]
    private ?string $state = null;

    #[ORM\Column(length: 255, nullable: true, name:'stripeIntent')]
    private ?string $stripeIntent = null;

    #[ORM\Column( name:'subTotalPrice')]
    private ?float $subTotalPrice = null;

    #[ORM\Column( name:'totalPrice')]
    private ?float $totalPrice = null;

    #[ORM\OneToMany(mappedBy: 'orderObject', targetEntity: Purchase::class)]
    private Collection $purchases;

    #[ORM\ManyToOne(inversedBy: 'orders')]
    #[ORM\JoinColumn(name: 'idUser', referencedColumnName: 'idUser')]
    private ?User $user = null;

    public function __construct(User $user, $paymentIntent, Cart $cart)
    {
        $this->user = $user;
        $this->dateOrder = new \DateTime();
        $this->dateDelivery = null;
        $this->rateTPS = Constants::TPS;
        $this->rateTVQ = Constants::TVQ;
        $this->shippingCost = Constants::SHIPPING_COST;
        $this->state = Constants::orderState['preparing'];
        $this->stripeIntent = $paymentIntent;
        $this->totalPrice = $cart->getTotalPrice();
        $this->subTotalPrice = $cart->getSubTotalPrice();

        $this->purchases = new ArrayCollection();
    }


    public function getIdOrder(): ?int
    {
        return $this->idOrder;
    }

    public function getDateOrder(): ?\DateTimeInterface
    {
        return $this->dateOrder;
    }

    public function getDateDelivery(): ?\DateTimeInterface
    {
        return $this->dateDelivery;
    }

    public function setDateDelivery(?\DateTimeInterface $dateDelivery): self
    {
        $this->dateDelivery = $dateDelivery;

        return $this;
    }

    public function getRateTPS(): ?float
    {
        return $this->rateTPS;
    }


    public function getRateTVQ(): ?float
    {
        return $this->rateTVQ;
    }

    public function getTotalPrice(): ?float
    {
        return $this->totalPrice;
    }
    public function getSubTotalPrice(): ?float
    {
        return $this->subTotalPrice;
    }


    public function getShippingCost(): ?float
    {
        return $this->shippingCost;
    }


    public function getState(): ?string
    {
        return $this->state;
    }

    public function setState(string $state): self
    {
        $this->state = $state;

        return $this;
    }

    public function getStripeIntent(): ?string
    {
        return $this->stripeIntent;
    }



    /**
     * @return Collection<int, Purchase>
     */
    public function getPurchases(): Collection
    {
        return $this->purchases;
    }

    public function addPurchase(Purchase $purchase): self
    {
        if (!$this->purchases->contains($purchase)) {
            $this->purchases->add($purchase);
            $purchase->setOrderObject($this);
        }

        return $this;
    }


    public function getUser(): ?User
    {
        return $this->user;
    }

}
