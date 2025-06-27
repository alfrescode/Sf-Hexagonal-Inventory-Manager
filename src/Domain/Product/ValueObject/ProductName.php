<?php
namespace App\Domain\Product\ValueObject;

class ProductName
{
    private string $name;

    public function __construct(string $name)
    {
        if (empty($name)) {
            throw new \InvalidArgumentException('El nombre del producto no puede estar vacío');
        }
        
        if (strlen($name) > 255) {
            throw new \InvalidArgumentException('El nombre del producto no puede tener más de 255 caracteres');
        }
        
        $this->name = $name;
    }

    public function getValue(): string
    {
        return $this->name;
    }

    public function __toString(): string
    {
        return $this->name;
    }
}