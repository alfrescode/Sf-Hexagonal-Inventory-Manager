<?php

namespace App\Tests\Unit\Application\Command\DeleteProduct;

use App\Application\Command\DeleteProduct\DeleteProductCommand;
use App\Application\Command\DeleteProduct\DeleteProductHandler;
use App\Domain\Product\Contract\ProductRepositoryInterface;
use App\Domain\Product\Event\ProductDeletedEvent;
use App\Domain\Product\Exception\ProductNotFoundException;
use App\Domain\Product\Product;
use App\Domain\Product\ValueObject\ProductId;
use App\Domain\Product\ValueObject\ProductName;
use App\Domain\Product\ValueObject\ProductPrice;
use App\Domain\Product\ValueObject\ProductStock;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class DeleteProductHandlerTest extends TestCase
{
    private ProductRepositoryInterface $repository;
    private EventDispatcherInterface $eventDispatcher;
    private DeleteProductHandler $handler;

    protected function setUp(): void
    {
        $this->repository = $this->createMock(ProductRepositoryInterface::class);
        $this->eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $this->handler = new DeleteProductHandler($this->repository, $this->eventDispatcher);
    }

    /**
     * @test
     */
    public function should_delete_product_and_dispatch_event_when_product_exists(): void
    {
        // Arrange
        $productId = 'product-123';
        $command = new DeleteProductCommand($productId);
        
        $product = new Product(
            new ProductId($productId),
            new ProductName('Test Product'),
            'Test Description',
            new ProductPrice(100.0),
            new ProductStock(10),
            []
        );
        
        $this->repository->expects($this->once())
            ->method('find')
            ->with($this->callback(function(ProductId $id) use ($productId) {
                return $id->value() === $productId;
            }))
            ->willReturn($product);
            
        $this->repository->expects($this->once())
            ->method('delete')
            ->with($this->callback(function(ProductId $id) use ($productId) {
                return $id->value() === $productId;
            }));
            
        $this->eventDispatcher->expects($this->once())
            ->method('dispatch')
            ->with($this->callback(function($event) use ($productId) {
                return $event instanceof ProductDeletedEvent 
                    && $event->getProductId() === $productId;
            }));

        // Act
        $this->handler->__invoke($command);

        // Assert is handled by the mock expectations
    }

    /**
     * @test
     */
    public function should_throw_exception_when_product_not_found(): void
    {
        // Arrange
        $productId = 'non-existent-product';
        $command = new DeleteProductCommand($productId);
        
        $this->repository->expects($this->once())
            ->method('find')
            ->willReturn(null);
            
        $this->repository->expects($this->never())
            ->method('delete');
            
        $this->eventDispatcher->expects($this->never())
            ->method('dispatch');

        // Assert
        $this->expectException(ProductNotFoundException::class);
        
        // Act
        $this->handler->__invoke($command);
    }
}
