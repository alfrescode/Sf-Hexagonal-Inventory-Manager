<?php

namespace App\Domain\Product\ValueObject;

final class ProductId
{
    private string $value;


    public function __construct(string $id)
    {
        $this->value = $id;
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
