<?php

namespace App\Application\Command\CreateProduct;

/**
 * Comando para crear un producto.
 */
class CreateProductCommand
{
    /**
     * @param string $name Nombre del producto
     * @param float $price Precio del producto
     * @param int $stock Cantidad en stock
     */
    public function __construct(
        public readonly string $name,
        public readonly float $price,
        public readonly int $stock
    ) {
    }
}
