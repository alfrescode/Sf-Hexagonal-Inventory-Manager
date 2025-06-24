<?php
namespace App\Domain\Product\ValueObject;

final class ProductStock
{
    private int $value;

    public function __construct(int $stock)
    {
        if ($stock < 0) {
            throw new \InvalidArgumentException("El stock no puede ser negativo.");
        }
        $this->value = $stock;
    }

    public function value(): int
    {
        return $this->value;
    }
}
