<?php

declare(strict_types=1);

namespace App\Application\Query\ListProducts;

/**
 * DTO para retornar un resumen de los datos de un producto.
 */
final class ProductSummaryDTO
{
    /**
     * @param string $id ID del producto
     * @param string $name Nombre del producto
     * @param string $description Descripción del producto
     * @param float $price Precio del producto
     * @param int $stock Cantidad en stock
     * @param array $variants Variantes del producto (resumen)
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
