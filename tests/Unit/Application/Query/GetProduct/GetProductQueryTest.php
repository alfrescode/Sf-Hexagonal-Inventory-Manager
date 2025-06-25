<?php

namespace App\Tests\Unit\Application\Query\GetProduct;

use App\Application\Query\GetProduct\GetProductQuery;
use PHPUnit\Framework\TestCase;

class GetProductQueryTest extends TestCase
{
    /**
     * @test
     */
    public function should_create_query_with_id(): void
    {
        // Arrange
        $productId = 'product-123';

        // Act
        $query = new GetProductQuery($productId);

        // Assert
        $this->assertEquals($productId, $query->id);
    }
}
