<?php

namespace App\Tests\Unit\Application\Event;

use App\Application\Event\ProductUpdatedListener;
use App\Domain\Product\Event\ProductUpdatedEvent;
use App\Domain\Product\Product;
use App\Domain\Product\ValueObject\ProductId;
use App\Domain\Product\ValueObject\ProductName;
use App\Domain\Product\ValueObject\ProductPrice;
use App\Domain\Product\ValueObject\ProductStock;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class ProductUpdatedListenerTest extends TestCase
{
    public function testShouldLogProductUpdate(): void
    {
        $loggerMock = $this->createMock(LoggerInterface::class);
        $listener = new ProductUpdatedListener($loggerMock);

        $product = new Product(
            new ProductId('product-id'),
            new ProductName('Test Product'),
            'Descripción del producto actualizado', // Añadimos la descripción como string
            new ProductPrice(100),
            new ProductStock(10)
        );

        $event = new ProductUpdatedEvent($product);

        $loggerMock->expects($this->once())
            ->method('info')
            ->with($this->stringContains('product-id'));

        $listener->onProductUpdated($event);
    }
}
