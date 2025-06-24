<?php

declare(strict_types=1);

namespace App\Application\Query\ListProducts;

/**
 * DTO para solicitar un listado de productos con opciones de filtrado y paginación.
 */
final class ListProductsQuery
{
    /**
     * @param int $page Número de página (comienza en 1)
     * @param int $limit Cantidad de productos por página
     * @param array|null $filters Filtros opcionales para la búsqueda
     */
    public function __construct(
        public readonly int $page = 1,
        public readonly int $limit = 10,
        public readonly ?array $filters = null
    ) {
    }
}