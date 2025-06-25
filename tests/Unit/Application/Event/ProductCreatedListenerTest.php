<?php

namespace App\Tests\Unit\Application\Event;

use App\Application\Event\ProductCreatedListener;
use App\Domain\Product\Event\ProductCreatedEvent;
use App\Domain\Product\Product;
use App\Domain\Product\ValueObject\ProductId;
use App\Domain\Product\ValueObject\ProductName;
use App\Domain\Product\ValueObject\ProductPrice;
use App\Domain\Product\ValueObject\ProductStock;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class ProductCreatedListenerTest extends TestCase
{
    private LoggerInterface $logger;
    private ProductCreatedListener $listener;
    private Product $product;

    protected function setUp(): void
    {
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->listener = new ProductCreatedListener($this->logger);
        
        // Create a product for testing
        $this->product = new Product(
            new ProductId('product-123'),
            new ProductName('Test Product'),
            'Test Description',
            new ProductPrice(99.99),
            new ProductStock(10),
            []
        );
    }

    /**
     * @test
     */
    public function should_log_product_creation(): void
    {
        // Arrange
        $event = new ProductCreatedEvent($this->product);
        
        $this->logger->expects($this->once())
            ->method('info')
            ->with(
                'Producto creado',
                $this->callback(function(array $context) {
                    return $context['id'] === 'product-123'
                        && $context['name'] === 'Test Product'
                        && $context['price'] === 99.99
                        && $context['stock'] === 10;
                })
            );

        // Act
        $this->listener->onProductCreated($event);
    }

    /**
     * @test
     */
    public function should_subscribe_to_product_created_event(): void
    {
        // Act
        $subscribedEvents = ProductCreatedListener::getSubscribedEvents();
        
        // Assert
        $this->assertArrayHasKey(ProductCreatedEvent::class, $subscribedEvents);
        $this->assertEquals('onProductCreated', $subscribedEvents[ProductCreatedEvent::class]);
    }
}
