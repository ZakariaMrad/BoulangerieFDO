<?php

namespace App\Entity;

use App\Repository\CategoryRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CategoryRepository::class)]
#[ORM\Table(name: 'categories')]
class Category
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name:"idCategory")]
    private ?int $idCategory = null;

    #[ORM\Column(length: 255,name:"name")]
    private ?string $name = null;

    #[ORM\OneToMany(targetEntity:Product::class, mappedBy: "category", fetch: "LAZY")]
    private $products;
    public function getIdCategory(): ?int
    {
        return $this->idCategory;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getProducts():Collection{
        return $this->products;
    }
}
