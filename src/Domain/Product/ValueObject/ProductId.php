<?php

namespace App\Domain\Product\ValueObject;

use Symfony\Component\Uid\Uuid;

class ProductId
{
    private string $id;

    public function __construct(?string $id = null)
    {
        $this->id = $id ?? (string) Uuid::v4();
    }

    public function getValue(): string
    {
        return $this->id;
    }

    public function __toString(): string
    {
        return $this->id;
    }
}
