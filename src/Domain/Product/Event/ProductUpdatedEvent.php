<?php

namespace App\Domain\Product\Event;

use App\Domain\Product\Product;

/**
 * Evento que se dispara cuando se actualiza un producto existente.
 */
class ProductUpdatedEvent
{
    private Product $product;

    public function __construct(Product $product)
    {
        $this->product = $product;
    }

    public function getProductId(): Product
    {
        return $this->product;
    }

    public function getProduct(): Product
    {
        return $this->product;
    }
}
