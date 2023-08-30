<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
#[ORM\Table(name: 'products')]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'idProduct')]
    private ?int $idProduct = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column]
    private ?float $price = null;

    #[ORM\Column(name:"quantityInStock")]
    private ?int $quantityInStock = null;

    #[ORM\Column(length: 2000)]
    private ?string $description = null;

    #[ORM\Column(length: 255,name:"imagePath1")]
    private ?string $imagePath1 = null;
    #[ORM\Column(length: 255,name:"imagePath2")]
    private ?string $imagePath2 = null;
    #[ORM\Column(length: 255,name:"imagePath3")]
    private ?string $imagePath3 = null;

    #[ORM\Column('idCategory')]
    private ?int $idCategory = null;

    #[ORM\ManyToOne(targetEntity: Category::class, inversedBy: "products", cascade: ["persist"])]
    #[ORM\JoinColumn(name: 'idCategory', referencedColumnName: 'idCategory')]
    private $category;

    #[ORM\OneToMany(targetEntity: Production::class, mappedBy: "product", fetch: "LAZY")]
    private $productions;

    #[ORM\OneToMany(targetEntity: Comment::class, mappedBy: "product", fetch: "LAZY")]
    private $comments;

    public function getIdProduct(): ?int
    {
        return $this->idProduct;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function getQuantityInStock(): ?int
    {
        return $this->quantityInStock;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getImagePath1(): ?string
    {
        return $this->imagePath1;
    }
    public function getImagePath2(): ?string
    {
        return $this->imagePath2;
    }
    public function getImagePath3(): ?string
    {
        return $this->imagePath3;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }
    public function getProductions(): Collection
    {
        return $this->productions;
    }
    public function getComments(): Collection
    {
        return $this->comments;
    }
}