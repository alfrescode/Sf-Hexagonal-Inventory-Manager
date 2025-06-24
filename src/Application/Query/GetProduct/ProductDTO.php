<?php

declare(strict_types=1);

namespace App\Application\Query\GetProduct;

/**
 * DTO para retornar datos de un producto.
 */
final class ProductDTO
{
    /**
     * @param string $id ID del producto
     * @param string $name Nombre del producto
     * @param string $description Descripción del producto
     * @param float $price Precio del producto
     * @param int $stock Cantidad en stock
     * @param array $variants Variantes del producto
     */
    public function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly string $description,
        public readonly float $price,
        public readonly int $stock,
        public readonly array $variants
    ) {
    }
}