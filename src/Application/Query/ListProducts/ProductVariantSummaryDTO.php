<?php

declare(strict_types=1);

namespace App\Application\Query\ListProducts;

/**
 * DTO para retornar un resumen de los datos de una variante de producto.
 */
final class ProductVariantSummaryDTO
{
    /**
     * @param string $size Talla de la variante
     * @param string $color Color de la variante
     * @param float $price Precio de la variante
     */
    public function __construct(
        public readonly string $size,
        public readonly string $color,
        public readonly float $price
    ) {
    }
}
