<?php

declare(strict_types=1);

namespace App\Application\Query\GetProduct;

/**
 * DTO para solicitar un producto específico por su ID.
 */
final class GetProductQuery
{
    /**
     * @param string $id ID del producto a buscar
     */
    public function __construct(
        public readonly string $id
    ) {
    }
}