<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Infrastructure\Persistence\Doctrine\Entity\ProductEntity;


#[ORM\Entity]
#[ORM\Table(name: "product_variants")]
class ProductVariantEntity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private ?int $id = null;

    #[ORM\Column(type: "string", length: 50)]
    private string $size;

    #[ORM\Column(type: "string", length: 50)]
    private string $color;

    #[ORM\Column(type: "decimal", precision: 10, scale: 2)]
    private float $price;

    #[ORM\Column(type: "integer")]
    private int $stock;

    #[ORM\Column(type: "string", length: 255)]
    private string $imageUrl;

    #[ORM\ManyToOne(targetEntity: ProductEntity::class, inversedBy: "variants")]
    #[ORM\JoinColumn(nullable: false)]
    private ProductEntity $product;

    public function __construct(
        string $size,
        string $color,
        float $price,
        int $stock,
        string $imageUrl,
        ProductEntity $product
    ) {
        $this->size = $size;
        $this->color = $color;
        $this->price = $price;
        $this->stock = $stock;
        $this->imageUrl = $imageUrl;
        $this->product = $product;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSize(): string
    {
        return $this->size;
    }

    public function setSize(string $size): self
    {
        $this->size = $size;
        return $this;
    }

    public function getColor(): string
    {
        return $this->color;
    }

    public function setColor(string $color): self
    {
        $this->color = $color;
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

    public function getImageUrl(): string
    {
        return $this->imageUrl;
    }

    public function setImageUrl(string $imageUrl): self
    {
        $this->imageUrl = $imageUrl;
        return $this;
    }

    public function getProduct(): ProductEntity
    {
        return $this->product;
    }

    public function setProduct(?ProductEntity $product): self
    {
        $this->product = $product;
        return $this;
    }
}