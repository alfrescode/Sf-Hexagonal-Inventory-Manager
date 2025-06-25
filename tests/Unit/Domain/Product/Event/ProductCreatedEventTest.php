<?php

namespace App\Tests\Unit\Domain\Product\Event;

use App\Domain\Product\Event\ProductCreatedEvent;
use App\Domain\Product\Product;
use App\Domain\Product\ValueObject\ProductId;
use App\Domain\Product\ValueObject\ProductName;
use App\Domain\Product\ValueObject\ProductPrice;
use App\Domain\Product\ValueObject\ProductStock;
use PHPUnit\Framework\TestCase;

class ProductCreatedEventTest extends TestCase
{
    /**
     * @test
     */
    public function should_create_event_with_product(): void
    {
        // Arrange
        $product = new Product(
            new ProductId('product-123'),
            new ProductName('Test Product'),
            'Test Description',
            new ProductPrice(99.99),
            new ProductStock(10),
            []
        );
        
        // Act
        $event = new ProductCreatedEvent($product);
        
        // Assert
        $this->assertSame($product, $event->getProduct());
    }
}
