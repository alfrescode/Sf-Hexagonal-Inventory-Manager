<?php

namespace App\Domain\Product\Event;

/**
 * Evento que se dispara cuando se elimina un producto existente.
 */
class ProductDeletedEvent
{
    private string $productId;

    public function __construct(string $productId)
    {
        $this->productId = $productId;
    }

    public function getProduct(): string
    {
        return $this->productId;
    }

    public function getProductId(): string
    {
        return $this->productId;
    }
}
