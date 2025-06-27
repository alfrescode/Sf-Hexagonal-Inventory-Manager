<?php

namespace App\Domain\Product\Event;

use App\Domain\Product\Product;

class ProductCreatedEvent
{
    private Product $product;

    public function __construct(Product $product)
    {
        $this->product = $product;
    }

    public function getProduct(): Product
    {
        return $this->product;
    }

    public function getProductId(): string
    {
        return $this->product->getId()->value();
    }
}
