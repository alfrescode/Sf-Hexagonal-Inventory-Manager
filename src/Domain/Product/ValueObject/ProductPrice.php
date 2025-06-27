<?php

namespace App\Domain\Product\ValueObject;

class ProductPrice
{
    private float $price;

    public function __construct(float $price)
    {
        if ($price < 0) {
            throw new \InvalidArgumentException('El precio del producto no puede ser negativo');
        }
        
        $this->price = $price;
    }

    public function getValue(): float
    {
        return $this->price;
    }

    public function __toString(): string
    {
        return (string) $this->price;
    }
}
