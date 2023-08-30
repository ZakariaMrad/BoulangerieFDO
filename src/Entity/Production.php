<?php

namespace App\Entity;

use App\Repository\ProductionRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProductionRepository::class)]
#[ORM\Table(name: 'productions')]
class Production
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name:"idProduction")]
    private ?int $idProduction = null;

    #[ORM\Column(name:"productTimeStamp")]
    private ?int $productTimeStamp = null;

    #[ORM\Column(name:"productNumber")]
    private ?int $productNumber = null;

    #[ORM\Column(length: 40,name:"productChef")]
    private ?string $productChef = null;

    #[ORM\ManyToOne(targetEntity: Product::class, inversedBy: "productions", cascade: ["persist"])]
    #[ORM\JoinColumn(name: 'idProduct', referencedColumnName: 'idProduct')]
    private $product;

    public function getIdProduction(): ?int
    {
        return $this->idProduction;
    }

    public function getProductTimeStamp(): ?int
    {
        return $this->productTimeStamp;
    }

    public function getProductNumber(): ?int
    {
        return $this->productNumber;
    }
    public function getProductChef(): ?string
    {
        return $this->productChef;
    }
}