<?php

namespace App\Entity;

use App\Repository\ProduitsRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProduitsRepository::class)]
class Produits
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 80)]
    private ?string $name = null;

    #[ORM\Column(length: 100)]
    private ?string $marque = null;

    #[ORM\Column]
    private ?float $prix = null;
    
    #[ORM\ManyToOne(inversedBy: 'produits')]
    private ?CategoryProduits $category = null;

    #[ORM\Column(length: 100)]
    private ?string $img_produits = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getMarque(): ?string
    {
        return $this->marque;
    }

    public function setMarque(string $marque): self
    {
        $this->marque = $marque;

        return $this;
    }

    public function getPrix(): ?float
    {
        return $this->prix;
    }

    public function setPrix(float $prix): self
    {
        $this->prix = $prix;

        return $this;
    }

    public function getCategory(): ?CategoryProduits
    {
        return $this->category;
    }

    public function setCategory(?CategoryProduits $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function getImgProduits(): ?string
    {
        return $this->img_produits;
    }

    public function setImgProduits(string $img_produits): self
    {
        $this->img_produits = $img_produits;

        return $this;
    }

}
