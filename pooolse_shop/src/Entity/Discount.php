<?php

namespace App\Entity;

use App\Repository\DiscountRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DiscountRepository::class)]
class Discount
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    private ?int $freeItemTreshold = null;

    #[ORM\Column(nullable: true)]
    private ?float $percentage = null;

    #[ORM\Column(nullable: true)]
    private ?int $whole = null;

    #[ORM\OneToOne(inversedBy: 'discount', cascade: ['persist', 'remove'])]
    private ?Brand $brand = null;

    #[ORM\OneToOne(inversedBy: 'discount', cascade: ['persist', 'remove'])]
    private ?Product $product = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFreeItemTreshold(): ?int
    {
        return $this->freeItemTreshold;
    }

    public function setFreeItemTreshold(?int $freeItemTreshold): static
    {
        $this->freeItemTreshold = $freeItemTreshold;

        return $this;
    }

    public function getPercentage(): ?float
    {
        return $this->percentage;
    }

    public function setPercentage(?float $percentage): static
    {
        $this->percentage = $percentage;

        return $this;
    }

    public function getWhole(): ?int
    {
        return $this->whole;
    }

    public function setWhole(?int $whole): static
    {
        $this->whole = $whole;

        return $this;
    }

    public function getBrand(): ?Brand
    {
        return $this->brand;
    }

    public function setBrand(?Brand $brand): static
    {
        $this->brand = $brand;

        return $this;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): static
    {
        $this->product = $product;

        return $this;
    }
}
