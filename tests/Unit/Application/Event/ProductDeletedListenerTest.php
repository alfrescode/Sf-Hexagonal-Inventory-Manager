<?php

namespace App\Tests\Unit\Application\Event;

use App\Application\Event\ProductDeletedListener;
use App\Domain\Product\Event\ProductDeletedEvent;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class ProductDeletedListenerTest extends TestCase
{
    public function testShouldLogProductDeletion(): void
    {
        $loggerMock = $this->createMock(LoggerInterface::class);

        $listener = new ProductDeletedListener($loggerMock);

        $event = new ProductDeletedEvent('product-id');
        $loggerMock->expects($this->once())
            ->method('info')
            ->with('Product deleted: product-id');

        $listener->onProductDeleted($event);
    }
}
