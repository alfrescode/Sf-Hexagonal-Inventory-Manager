<?php

namespace App\Application\Command\DeleteProduct;

/**
 * Comando para eliminar un producto.
 */
class DeleteProductCommand
{
    /**
     * @param string $id Identificador del producto a eliminar
     */
    public function __construct(
        public readonly string $id
    ) {
    }
}
