<?php

declare(strict_types=1);

namespace App\Application\Query\ListProducts;

use App\Domain\Product\Contract\ProductRepositoryInterface;

/**
 * Manejador para listar productos con paginación y filtrado opcional.
 */
final class ListProductsHandler
{
    /**
     * @param ProductRepositoryInterface $repository Repositorio de productos
     */
    public function __construct(
        private readonly ProductRepositoryInterface $repository
    ) {
    }

    /**
     * Ejecuta la consulta para listar productos.
     *
     * @param ListProductsQuery $query Consulta con opciones de paginación y filtrado
     * @return ProductsListDTO Lista de productos paginada
     */
    public function __invoke(ListProductsQuery $query): ProductsListDTO
    {
        // Obtener todos los productos del repositorio junto con el total
        [$products, $total] = $this->repository->findAll($query->page, $query->limit);
        
        // Mapear los productos a DTOs
        $productDTOs = [];
        foreach ($products as $product) {
            $variantDTOs = [];
            
            foreach ($product->getVariants() as $variant) {
                $variantDTOs[] = new ProductVariantSummaryDTO(
                    $variant->getSize(),
                    $variant->getColor(),
                    $variant->getPrice()->value()
                );
            }
            
            $productDTOs[] = new ProductSummaryDTO(
                $product->getId()->value(),
                $product->getName()->value(),
                $product->getDescription(),
                $product->getPrice()->value(),
                $product->getStock()->value(),
                $variantDTOs
            );
        }
        
        // Calcular el total de páginas
        $totalPages = (int)ceil($total / $query->limit);
        
        // Crear y devolver el DTO con la lista de productos
        return new ProductsListDTO(
            $productDTOs,
            $query->page,
            $query->limit,
            $total,
            $totalPages
        );
    }
}
