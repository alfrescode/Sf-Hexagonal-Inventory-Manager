<?php

namespace App\Tests\Unit\Application\Query\ListProducts;

use App\Application\Query\ListProducts\ListProductsQuery;
use PHPUnit\Framework\TestCase;

class ListProductsQueryTest extends TestCase
{
    /**
     * @test
     */
    public function should_create_query_with_default_values(): void
    {
        // Act
        $query = new ListProductsQuery();

        // Assert
        $this->assertEquals(1, $query->page);
        $this->assertEquals(10, $query->limit);
    }

    /**
     * @test
     */
    public function should_create_query_with_custom_values(): void
    {
        // Arrange
        $page = 2;
        $limit = 20;

        // Act
        $query = new ListProductsQuery($page, $limit);

        // Assert
        $this->assertEquals($page, $query->page);
        $this->assertEquals($limit, $query->limit);
    }
}
