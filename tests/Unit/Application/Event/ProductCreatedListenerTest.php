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
    public function testShouldLogProductCreation(): void
    {
        $loggerMock = $this->createMock(LoggerInterface::class);
        $listener = new ProductCreatedListener($loggerMock);

        $product = new Product(
            new ProductId('product-id'),
            new ProductName('Test Product'),
            'Descripción del producto',  // Añadimos la descripción como string
            new ProductPrice(100),
            new ProductStock(10)
        );

        $event = new ProductCreatedEvent($product);

        $loggerMock->expects($this->once())
            ->method('info')
            ->with($this->stringContains('product-id'));

        $listener->onProductCreated($event);
    }
}
