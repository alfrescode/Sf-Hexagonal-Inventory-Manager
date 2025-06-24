<?php

declare(strict_types=1);

namespace App\Application\Query\GetProduct;

/**
 * DTO para retornar datos de una variante de producto.
 */
final class ProductVariantDTO
{
    /**
     * @param string $size Talla de la variante
     * @param string $color Color de la variante
     * @param float $price Precio de la variante
     * @param int $stock Cantidad en stock de la variante
     * @param string $imageUrl URL de la imagen de la variante
     */
    public function __construct(
        public readonly string $size,
        public readonly string $color,
        public readonly float $price,
        public readonly int $stock,
        public readonly string $imageUrl
    ) {
    }
}