<?php

namespace App\Entity;

use App\Repository\CommentRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CommentRepository::class)]
#[ORM\Table(name:'comments')]
class Comment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name:'idComment')]
    private ?int $idComment = null;

    #[ORM\Column(length: 255)]
    private ?string $username = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column(length: 150)]
    private ?string $content = null;

    #[ORM\Column]
    private ?int $score = null;

    #[ORM\ManyToOne(targetEntity: Product::class, inversedBy: "comments", cascade: ["persist"])]
    #[ORM\JoinColumn(name: 'idProduct', referencedColumnName: 'idProduct')]
    private $product;


    public function getIdComment(): ?int
    {
        return $this->idComment;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function getScore(): ?int
    {
        return $this->score;
    }
}
