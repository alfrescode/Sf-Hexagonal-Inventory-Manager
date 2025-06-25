<?php

namespace App\Tests\Unit\Domain\Product\Event;

use App\Domain\Product\Event\ProductUpdatedEvent;
use App\Domain\Product\Product;
use App\Domain\Product\ValueObject\ProductId;
use App\Domain\Product\ValueObject\ProductName;
use App\Domain\Product\ValueObject\ProductPrice;
use App\Domain\Product\ValueObject\ProductStock;
use PHPUnit\Framework\TestCase;

class ProductUpdatedEventTest extends TestCase
{
    /**
     * @test
     */
    public function should_create_event_with_product(): void
    {
        // Arrange
        $product = new Product(
            new ProductId('product-123'),
            new ProductName('Updated Product'),
            'Updated Description',
            new ProductPrice(149.99),
            new ProductStock(15),
            []
        );
        
        // Act
        $event = new ProductUpdatedEvent($product);
        
        // Assert
        $this->assertSame($product, $event->getProduct());
    }
}
