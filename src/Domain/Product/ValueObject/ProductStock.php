<?php

namespace App\Domain\Product\ValueObject;

class ProductStock
{
    private int $stock;

    public function __construct(int $stock)
    {
        if ($stock < 0) {
            throw new \InvalidArgumentException('El stock del producto no puede ser negativo');
        }
        
        $this->stock = $stock;
    }

    public function getValue(): int
    {
        return $this->stock;
    }

    public function __toString(): string
    {
        return (string) $this->stock;
    }
}
