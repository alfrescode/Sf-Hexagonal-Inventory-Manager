<?php

namespace App\Domain\Product\Service;

use App\Domain\Product\Contract\ProductRepositoryInterface;
use App\Domain\Product\Product;
use App\Domain\Product\ValueObject\ProductId;
use App\Domain\Product\ValueObject\ProductStock;
use App\Domain\Product\Exception\ProductNotFoundException;
use App\Domain\Product\Exception\InsufficientStockException;

/**
 * Servicio de dominio para operaciones complejas con productos
 */
class ProductService
{
    private ProductRepositoryInterface $repository;

    public function __construct(ProductRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Busca productos por criterios avanzados
     *
     * @param array $criteria Criterios de búsqueda (nombre, precio mínimo, precio máximo, etc.)
     * @return Product[] Lista de productos que cumplen los criterios
     */
    public function findByCriteria(array $criteria): array
    {
        // Esta es una implementación simulada. En una implementación real,
        // se delegaría esta búsqueda al repositorio con capacidades avanzadas.
        
        // Por ahora, obtenemos todos los productos y filtramos en memoria
        [$allProducts, $total] = $this->repository->findAll();
        
        $filteredProducts = [];
        
        foreach ($allProducts as $product) {
            $match = true;
            
            // Filtrar por nombre
            if (isset($criteria['name']) && !$this->matchesName($product, $criteria['name'])) {
                $match = false;
            }
            
            // Filtrar por precio mínimo
            if (isset($criteria['minPrice']) && $product->getPrice()->value() < $criteria['minPrice']) {
                $match = false;
            }
            
            // Filtrar por precio máximo
            if (isset($criteria['maxPrice']) && $product->getPrice()->value() > $criteria['maxPrice']) {
                $match = false;
            }
            
            // Filtrar por stock mínimo
            if (isset($criteria['minStock']) && $product->getStock()->value() < $criteria['minStock']) {
                $match = false;
            }
            
            if ($match) {
                $filteredProducts[] = $product;
            }
        }
        
        return $filteredProducts;
    }
    
    /**
     * Reduce el stock de un producto en la cantidad especificada
     *
     * @param ProductId $productId ID del producto
     * @param int $quantity Cantidad a reducir
     * @throws ProductNotFoundException Si el producto no existe
     * @throws InsufficientStockException Si no hay suficiente stock
     */
    public function reduceStock(ProductId $productId, int $quantity): void
    {
        $product = $this->repository->find($productId);
        
        if (!$product) {
            throw new ProductNotFoundException("Producto con ID {$productId->value()} no encontrado");
        }
        
        $currentStock = $product->getStock()->value();
        
        if ($currentStock < $quantity) {
            throw new InsufficientStockException(
                "Stock insuficiente para el producto {$productId->value()}. " .
                "Stock actual: {$currentStock}, Solicitado: {$quantity}"
            );
        }
        
        $product->setStock(new ProductStock($currentStock - $quantity));
        $this->repository->save($product);
    }
    
    /**
     * Aumenta el stock de un producto en la cantidad especificada
     *
     * @param ProductId $productId ID del producto
     * @param int $quantity Cantidad a aumentar
     * @throws ProductNotFoundException Si el producto no existe
     */
    public function increaseStock(ProductId $productId, int $quantity): void
    {
        $product = $this->repository->find($productId);
        
        if (!$product) {
            throw new ProductNotFoundException("Producto con ID {$productId->value()} no encontrado");
        }
        
        $currentStock = $product->getStock()->value();
        $product->setStock(new ProductStock($currentStock + $quantity));
        $this->repository->save($product);
    }
    
    /**
     * Verifica si el nombre del producto contiene la cadena de búsqueda
     */
    private function matchesName(Product $product, string $searchTerm): bool
    {
        return stripos($product->getName()->value(), $searchTerm) !== false;
    }
}
