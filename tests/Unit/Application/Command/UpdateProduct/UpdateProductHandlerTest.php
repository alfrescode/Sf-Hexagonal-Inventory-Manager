<?php

namespace App\Tests\Unit\Application\Command\UpdateProduct;

use App\Application\Command\UpdateProduct\UpdateProductCommand;
use App\Application\Command\UpdateProduct\UpdateProductHandler;
use App\Domain\Product\Contract\ProductRepositoryInterface;
use App\Domain\Product\Event\ProductUpdatedEvent;
use App\Domain\Product\Exception\ProductNotFoundException;
use App\Domain\Product\Product;
use App\Domain\Product\ProductVariant;
use App\Domain\Product\ValueObject\ProductId;
use App\Domain\Product\ValueObject\ProductName;
use App\Domain\Product\ValueObject\ProductPrice;
use App\Domain\Product\ValueObject\ProductStock;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class UpdateProductHandlerTest extends TestCase
{
    private ProductRepositoryInterface $repository;
    private EventDispatcherInterface $eventDispatcher;
    private UpdateProductHandler $handler;
    private Product $product;

    protected function setUp(): void
    {
        $this->repository = $this->createMock(ProductRepositoryInterface::class);
        $this->eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $this->handler = new UpdateProductHandler($this->repository, $this->eventDispatcher);
        
        // Create a product for testing
        $this->product = new Product(
            new ProductId('123'),
            new ProductName('Original Product'),
            'Original Description',
            new ProductPrice(100.0),
            new ProductStock(5),
            []
        );
    }

    /**
     * @test
     */
    public function should_update_product_and_dispatch_event_when_product_exists(): void
    {
        // Arrange
        $id = '123';
        $newName = 'Updated Product';
        $newDescription = 'Updated Description';
        $newPrice = 150.0;
        $newStock = 10;
        $newVariants = [
            [
                'size' => 'L',
                'color' => 'Blue',
                'price' => 145.0,
                'stock' => 8,
                'imageUrl' => 'http://example.com/updated.jpg'
            ]
        ];
        
        $command = new UpdateProductCommand($id, $newName, $newDescription, $newPrice, $newStock, $newVariants);
        
        $this->repository->expects($this->once())
            ->method('find')
            ->with($this->callback(function(ProductId $productId) use ($id) {
                return $productId->value() === $id;
            }))
            ->willReturn($this->product);
            
        $this->repository->expects($this->once())
            ->method('save')
            ->with($this->callback(function(Product $product) use ($newName, $newDescription, $newPrice, $newStock) {
                return $product->getName()->value() === $newName
                    && $product->getDescription() === $newDescription
                    && $product->getPrice()->value() === $newPrice
                    && $product->getStock()->value() === $newStock
                    && count($product->getVariants()) === 1;
            }));
            
        $this->eventDispatcher->expects($this->once())
            ->method('dispatch')
            ->with($this->isInstanceOf(ProductUpdatedEvent::class));

        // Act
        $this->handler->__invoke($command);

        // Assert is handled by the mock expectations
    }

    /**
     * @test
     */
    public function should_update_only_specified_fields(): void
    {
        // Arrange
        $id = '123';
        $newName = 'Updated Product';
        // Only update name, leave other fields unchanged
        $command = new UpdateProductCommand($id, $newName);
        
        $this->repository->expects($this->once())
            ->method('find')
            ->willReturn($this->product);
            
        $this->repository->expects($this->once())
            ->method('save')
            ->with($this->callback(function(Product $product) use ($newName) {
                return $product->getName()->value() === $newName
                    // Original values should be unchanged
                    && $product->getDescription() === 'Original Description'
                    && $product->getPrice()->value() === 100.0
                    && $product->getStock()->value() === 5;
            }));
            
        $this->eventDispatcher->expects($this->once())
            ->method('dispatch')
            ->with($this->isInstanceOf(ProductUpdatedEvent::class));

        // Act
        $this->handler->__invoke($command);
    }

    /**
     * @test
     */
    public function should_throw_exception_when_product_not_found(): void
    {
        // Arrange
        $id = '999'; // Non-existing ID
        $command = new UpdateProductCommand($id, 'Updated Name');
        
        $this->repository->expects($this->once())
            ->method('find')
            ->willReturn(null);
            
        $this->repository->expects($this->never())
            ->method('save');
            
        $this->eventDispatcher->expects($this->never())
            ->method('dispatch');

        // Assert
        $this->expectException(ProductNotFoundException::class);
        
        // Act
        $this->handler->__invoke($command);
    }
}
