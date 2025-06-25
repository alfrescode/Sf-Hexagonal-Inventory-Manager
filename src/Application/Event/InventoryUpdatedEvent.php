<?php

namespace App\Application\Event;

/**
 * Evento que se dispara cuando se actualiza el inventario de un producto
 */
class InventoryUpdatedEvent
{
    private string $productId;
    private int $quantityChange;
    private string $reason;
    
    public function __construct(string $productId, int $quantityChange, string $reason)
    {
        $this->productId = $productId;
        $this->quantityChange = $quantityChange;
        $this->reason = $reason;
    }
    
    public function getProductId(): string
    {
        return $this->productId;
    }
    
    public function getQuantityChange(): int
    {
        return $this->quantityChange;
    }
    
    public function getReason(): string
    {
        return $this->reason;
    }
}
