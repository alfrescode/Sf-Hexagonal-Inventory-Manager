<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity]
#[ORM\Table(name: "products")]
class ProductEntity
{
    #[ORM\Id]
    #[ORM\Column(type: "string", length: 36)]
    private string $id;

    #[ORM\Column(type: "string", length: 255)]
    private string $name;

    #[ORM\Column(type: "text", nullable: true)]
    private ?string $description;

    #[ORM\Column(type: "decimal", precision: 10, scale: 2)]
    private float $price;

    #[ORM\Column(type: "integer")]
    private int $stock;

    #[ORM\OneToMany(
        mappedBy: "product",
        targetEntity: ProductVariantEntity::class,
        cascade: ["persist", "remove"],
        orphanRemoval: true
    )]
    private Collection $variants;

    public function __construct(string $id, string $name, float $price, int $stock, ?string $description = null)
    {
        $this->id = $id;
        $this->name = $name;
        $this->price = $price;
        $this->stock = $stock;
        $this->description = $description;
        $this->variants = new ArrayCollection();
    }

    public function getId(): string 
    {
        return $this->id;
    }

    public function getName(): string 
    {
        return $this->name;
    }

    public function setName(string $name): self 
    {
        $this->name = $name;
        return $this;
    }

    public function getDescription(): ?string 
    {
        return $this->description;
    }

    public function setDescription(?string $description): self 
    {
        $this->description = $description;
        return $this;
    }

    public function getPrice(): float 
    {
        return $this->price;
    }

    public function setPrice(float $price): self 
    {
        $this->price = $price;
        return $this;
    }

    public function getStock(): int 
    {
        return $this->stock;
    }

    public function setStock(int $stock): self 
    {
        $this->stock = $stock;
        return $this;
    }

    public function getVariants(): Collection 
    {
        return $this->variants;
    }

    public function addVariant(ProductVariantEntity $variant): self 
    {
        if (!$this->variants->contains($variant)) {
            $this->variants->add($variant);
            $variant->setProduct($this);
        }
        return $this;
    }

    public function removeVariant(ProductVariantEntity $variant): self 
    {
        if ($this->variants->removeElement($variant)) {
            if ($variant->getProduct() === $this) {
                $variant->setProduct(null);
            }
        }
        return $this;
    }
}