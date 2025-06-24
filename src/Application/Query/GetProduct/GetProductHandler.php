<?php

declare(strict_types=1);

namespace App\Application\Query\GetProduct;

use App\Domain\Product\Contract\ProductRepositoryInterface;
use App\Domain\Product\Exception\ProductNotFoundException;
use App\Domain\Product\ValueObject\ProductId;

/**
 * Manejador para obtener un producto específico por su ID.
 */
final class GetProductHandler
{
    /**
     * @param ProductRepositoryInterface $repository Repositorio de productos
     */
    public function __construct(
        private readonly ProductRepositoryInterface $repository
    ) {
    }

    /**
     * Ejecuta la consulta para obtener un producto específico.
     *
     * @param GetProductQuery $query Consulta con el ID del producto
     * @return ProductDTO Datos del producto encontrado
     * @throws ProductNotFoundException Si el producto no existe
     */
    public function __invoke(GetProductQuery $query): ProductDTO
    {
        $product = $this->repository->find(new ProductId($query->id));
        
        if (!$product) {
            throw new ProductNotFoundException("Producto con ID {$query->id} no encontrado");
        }
        
        // Mapear las variantes a DTOs
        $variantDTOs = [];
        foreach ($product->getVariants() as $variant) {
            $variantDTOs[] = new ProductVariantDTO(
                $variant->getSize(),
                $variant->getColor(),
                $variant->getPrice()->value(),
                $variant->getStock()->value(),
                $variant->getImageUrl()
            );
        }
        
        // Crear y devolver el DTO del producto
        return new ProductDTO(
            $product->getId()->value(),
            $product->getName()->value(),
            $product->getDescription(),
            $product->getPrice()->value(),
            $product->getStock()->value(),
            $variantDTOs
        );
    }
}