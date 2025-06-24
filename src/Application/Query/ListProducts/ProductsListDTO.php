<?php

declare(strict_types=1);

namespace App\Application\Query\ListProducts;

/**
 * DTO para retornar una lista paginada de productos.
 */
final class ProductsListDTO
{
    /**
     * @param array $products Lista de productos (ProductSummaryDTO[])
     * @param int $page Página actual
     * @param int $limit Límite de productos por página
     * @param int $total Total de productos
     * @param int $totalPages Total de páginas
     */
    public function __construct(
        public readonly array $products,
        public readonly int $page,
        public readonly int $limit,
        public readonly int $total,
        public readonly int $totalPages
    ) {
    }
}
