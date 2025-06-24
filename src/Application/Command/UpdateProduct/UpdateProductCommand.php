<?php
namespace App\Application\Command\UpdateProduct;

class UpdateProductCommand
{
    public string $id;
    public ?string $name;
    public ?string $description;
    public ?float $price;
    public ?int $stock;
    public ?array $variants;

    public function __construct(
        string $id,
        ?string $name = null,
        ?string $description = null,
        ?float $price = null,
        ?int $stock = null,
        ?array $variants = null
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
        $this->price = $price;
        $this->stock = $stock;
        $this->variants = $variants;
    }
}