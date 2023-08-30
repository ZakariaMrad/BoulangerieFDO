<?php

namespace App\Entity;

use App\Repository\PurchaseRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PurchaseRepository::class)]
#[ORM\Table(name: 'purchases')]
class Purchase
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name:'idPurchase')]
    private ?int $idPurchase = null;

    #[ORM\Column]
    private ?int $quantity = null;

    #[ORM\Column(name:'priceForOne')]
    private ?float $priceForOne = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(name: 'idProduct', referencedColumnName: 'idProduct',nullable: false)]
    private ?Product $product = null;

    #[ORM\ManyToOne(inversedBy: 'purchase')]
    #[ORM\JoinColumn(name: 'idOrder', referencedColumnName: 'idOrder')]
    private ?Order $orderObject = null;

    
    public function __construct(Product $product){
        $this->quantity = 1;
        $this->priceForOne = $product->getPrice();
        $this->product = $product;
    }

    public function getIdPurchase(): ?int
    {
        return $this->idPurchase;
    }


    public function getQuantity(): ?int
    {
        return $this->quantity;
    }
    public function setQuantity($quantity){
        $this->quantity=$quantity;
        return $this->quantity;
    }

    public function getPriceForOne(): ?float
    {
        return $this->priceForOne;
    }
    public function getPurchasePrice(): ?float
    {
        return $this->priceForOne* $this->quantity;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }
    public function getOrderObject(): ?Order
    {
        return $this->orderObject;
    }

    public function setOrderObject(?Order $orderObject): self
    {
        $this->orderObject = $orderObject;

        return $this;
    }

}
