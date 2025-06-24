<?php
namespace App\Domain\Product\ValueObject;

final class ProductPrice
{
    private float $value;

    public function __construct(float $price)
    {
        if ($price < 0) {
            throw new \InvalidArgumentException("El precio no puede ser negativo.");
        }
        $this->value = $price;
    }

    public function value(): float
    {
        return $this->value;
    }
}
