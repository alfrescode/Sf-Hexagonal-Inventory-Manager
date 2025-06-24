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

    // Comentario indicando que aquí irían los métodos getters para acceder a las propiedades privadas.
    // Getters...
}