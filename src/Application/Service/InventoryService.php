<?php

namespace App\Application\Service;

use App\Domain\Product\ValueObject\ProductId;
use App\Domain\Product\Service\ProductService;
use App\Domain\Product\Exception\ProductNotFoundException;
use App\Domain\Product\Exception\InsufficientStockException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use App\Application\Event\InventoryUpdatedEvent;

/**
 * Servicio de aplicación para operaciones de inventario
 */
class InventoryService
{
    private ProductService $productService;
    private ?EventDispatcherInterface $eventDispatcher;
    
    public function __construct(
        ProductService $productService,
        ?EventDispatcherInterface $eventDispatcher = null
    ) {
        $this->productService = $productService;
        $this->eventDispatcher = $eventDispatcher;
    }
    
    /**
     * Ajusta el stock de un producto
     *
     * @param string $productId ID del producto
     * @param int $quantity Cantidad a ajustar (positiva para aumentar, negativa para reducir)
     * @param string $reason Motivo del ajuste
     * @throws ProductNotFoundException Si el producto no existe
     * @throws InsufficientStockException Si la reducción lleva a stock negativo
     */
    public function adjustStock(string $productId, int $quantity, string $reason): void
    {
        $productIdObj = new ProductId($productId);
        
        if ($quantity > 0) {
            $this->productService->increaseStock($productIdObj, $quantity);
        } else if ($quantity < 0) {
            $this->productService->reduceStock($productIdObj, abs($quantity));
        }
        
        // Disparar evento
        if ($this->eventDispatcher) {
            $this->eventDispatcher->dispatch(
                new InventoryUpdatedEvent($productId, $quantity, $reason)
            );
        }
    }
    
    /**
     * Busca productos por criterios específicos
     *
     * @param array $criteria Criterios de búsqueda
     * @return array Productos que cumplen los criterios
     */
    public function searchProducts(array $criteria): array
    {
        return $this->productService->findByCriteria($criteria);
    }
}
