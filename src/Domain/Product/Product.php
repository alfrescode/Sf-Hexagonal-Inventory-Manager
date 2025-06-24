<?php
namespace App\Domain\Product;

use App\Domain\Product\ValueObject\ProductId;
use App\Domain\Product\ValueObject\ProductName;
use App\Domain\Product\ValueObject\ProductPrice;
use App\Domain\Product\ValueObject\ProductStock;

final class Product
{
    private ProductId $id;
    private ProductName $name;
    private string $description;
    private ProductPrice $price;
    private ProductStock $stock;
    /** 
     * @var ProductVariant[] 
     */
    private array $variants;

    public function __construct(
        ProductId $id,
        ProductName $name,
        string $description,
        ProductPrice $price,
        ProductStock $stock,
        array $variants = []
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
        $this->price = $price;
        $this->stock = $stock;
        $this->variants = $variants;
    }

    // Aquí irían los métodos getters para acceder a las propiedades.
    // Getters...
}