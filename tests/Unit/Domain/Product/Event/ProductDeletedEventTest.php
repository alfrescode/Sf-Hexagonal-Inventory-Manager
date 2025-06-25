<?php

namespace App\Tests\Unit\Domain\Product\Event;

use App\Domain\Product\Event\ProductDeletedEvent;
use PHPUnit\Framework\TestCase;

class ProductDeletedEventTest extends TestCase
{
    /**
     * @test
     */
    public function should_create_event_with_product_id(): void
    {
        // Arrange
        $productId = 'product-123';
        
        // Act
        $event = new ProductDeletedEvent($productId);
        
        // Assert
        $this->assertEquals($productId, $event->getProductId());
    }
}
