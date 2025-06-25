<?php

namespace App\Tests\Unit\Application\Event;

use App\Application\Event\ProductDeletedListener;
use App\Domain\Product\Event\ProductDeletedEvent;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class ProductDeletedListenerTest extends TestCase
{
    private LoggerInterface $logger;
    private ProductDeletedListener $listener;

    protected function setUp(): void
    {
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->listener = new ProductDeletedListener($this->logger);
    }

    /**
     * @test
     */
    public function should_log_product_deletion(): void
    {
        // Arrange
        $productId = 'product-123';
        $event = new ProductDeletedEvent($productId);
        
        $this->logger->expects($this->once())
            ->method('info')
            ->with(
                'Producto eliminado',
                $this->callback(function(array $context) use ($productId) {
                    return $context['id'] === $productId;
                })
            );

        // Act
        $this->listener->onProductDeleted($event);
    }

    /**
     * @test
     */
    public function should_subscribe_to_product_deleted_event(): void
    {
        // Act
        $subscribedEvents = ProductDeletedListener::getSubscribedEvents();
        
        // Assert
        $this->assertArrayHasKey(ProductDeletedEvent::class, $subscribedEvents);
        $this->assertEquals('onProductDeleted', $subscribedEvents[ProductDeletedEvent::class]);
    }
}
