<?php
namespace App\UI\Rest\DTO;

class ProductResponse
{
    public string $id;
    public string $name;
    public string $description;
    public float $price;
    public int $stock;
    /** @var array<int, array> */
    public array $variants = [];

    public function __construct(
        string $id,
        string $name,
        string $description,
        float $price,
        int $stock,
        array $variants = []
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
        $this->price = $price;
        $this->stock = $stock;
        $this->variants = $variants;
    }
}