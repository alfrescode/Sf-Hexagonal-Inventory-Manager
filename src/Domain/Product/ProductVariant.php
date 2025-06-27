<?php
namespace App\Domain\Product;

use App\Domain\Product\ValueObject\ProductPrice;
use App\Domain\Product\ValueObject\ProductStock;

final class ProductVariant
{
    private string $size;
    private string $color;
    private ProductPrice $price;
    private ProductStock $stock;
    private string $imageUrl;

    public function __construct(string $size, string $color, ProductPrice $price, ProductStock $stock, string $imageUrl)
    {
        $this->size = $size;
        $this->color = $color;
        $this->price = $price;
        $this->stock = $stock;
        $this->imageUrl = $imageUrl;
    }

    //getters y setters para acceder y modificar las propiedades privadas.
    public function getSize(): string
    {
        return $this->size;
    }

    public function getColor(): string
    {
        return $this->color;
    }

    public function getPrice(): ProductPrice
    {
        return $this->price;
    }

    public function getStock(): ProductStock
    {
        return $this->stock;
    }

    public function getImageUrl(): string
    {
        return $this->imageUrl;
    }

    public function setSize(string $size): void
    {
        $this->size = $size;
    }

    public function setColor(string $color): void
    {
        $this->color = $color;
    }

    public function setPrice(ProductPrice $price): void
    {
        $this->price = $price;
    }

    public function setStock(ProductStock $stock): void
    {
        $this->stock = $stock;
    }

    public function setImageUrl(string $imageUrl): void
    {
        $this->imageUrl = $imageUrl;
    }
}