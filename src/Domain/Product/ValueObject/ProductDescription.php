<?php

namespace App\Domain\Product\ValueObject;

class ProductDescription
{
    private string $description;

    public function __construct(string $description)
    {
        $this->description = $description;
    }

    public function getValue(): string
    {
        return $this->description;
    }

    public function __toString(): string
    {
        return $this->description;
    }
}