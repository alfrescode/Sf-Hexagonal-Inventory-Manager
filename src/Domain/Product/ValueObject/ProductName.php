<?php
namespace App\Domain\Product\ValueObject;

final class ProductName
{
    private string $value;

    public function __construct(string $name)
    {
        if (empty($name)) {
            throw new \InvalidArgumentException("El nombre del producto no puede estar vacío.");
        }
        $this->value = $name;
    }

    public function value(): string
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}