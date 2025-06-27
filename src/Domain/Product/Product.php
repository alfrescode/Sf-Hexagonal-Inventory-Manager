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

    //implementamos los métodos getters y setters necesarios para acceder y modificar las propiedades privadas.
    public function getId(): ProductId
    {
        return $this->id;
    }
    public function getName(): ProductName
    {
        return $this->name;
    }
    public function setName(ProductName $name): void
    {        $this->name = $name;
    }
    public function getDescription(): string
    {
        return $this->description;
    }
    public function setDescription(string $description): void
    {
        $this->description = $description;
    }
    public function getPrice(): ProductPrice
    {
        return $this->price;
    }
    public function setPrice(ProductPrice $price): void 
    {
        $this->price = $price;
    }
    public function getStock(): ProductStock
    {
        return $this->stock;
    }
    public function setStock(ProductStock $stock): void
    {
        $this->stock = $stock;
    }
    public function setVariants(array $variants): void
    {
        $this->variants = $variants;
    }
    /**
     * @return ProductVariant[]
     */
    public function getVariants(): array
    {
        return $this->variants;
    }

    public function __toString(): string
    {
        return sprintf(
            'Product: %s, Name: %s, Description: %s, Price: %s, Stock: %s',
            (string)$this->id,
            (string)$this->name,
            $this->description,
            (string)$this->price,
            (string)$this->stock
        );
    }
}