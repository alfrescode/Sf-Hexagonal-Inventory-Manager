<?php

namespace App\Tests\Unit\Application\Command\CreateProduct;

use App\Application\Command\CreateProduct\CreateProductCommand;
use App\Application\Command\CreateProduct\CreateProductHandler;
use App\Domain\Product\Contract\ProductRepositoryInterface;
use App\Domain\Product\Event\ProductCreatedEvent;
use App\Domain\Product\Product;
use App\Domain\Product\ValueObject\ProductId;
use App\Domain\Product\ValueObject\ProductName;
use App\Domain\Product\ValueObject\ProductPrice;
use App\Domain\Product\ValueObject\ProductStock;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Uid\Uuid;

class CreateProductHandlerTest extends TestCase
{
    private ProductRepositoryInterface $repository;
    private EventDispatcherInterface $eventDispatcher;
    private CreateProductHandler $handler;

    protected function setUp(): void
    {
        $this->repository = $this->createMock(ProductRepositoryInterface::class);
        $this->eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $this->handler = new CreateProductHandler($this->repository, $this->eventDispatcher);
    }

    /**
     * @test
     */
    public function should_create_product_and_dispatch_event(): void
    {
        // Arrange
        $name = 'Test Product';
        $description = 'Product description';
        $price = 99.99;
        $stock = 10;
        $variants = [
            [
                'size' => 'M',
                'color' => 'Red',
                'price' => 89.99,
                'stock' => 5,
                'imageUrl' => 'http://example.com/image.jpg'
            ]
        ];

        $command = new CreateProductCommand($name, $description, $price, $stock, $variants);

        $this->repository->expects($this->once())
            ->method('save')
            ->with($this->callback(function(Product $product) use ($name, $description, $price, $stock) {
                return $product->getName()->value() === $name
                    && $product->getDescription() === $description
                    && $product->getPrice()->value() === $price
                    && $product->getStock()->value() === $stock
                    && count($product->getVariants()) === 1;
            }));
            
        $this->eventDispatcher->expects($this->once())
            ->method('dispatch')
            ->with($this->isInstanceOf(ProductCreatedEvent::class));

        // Act
        $this->handler->__invoke($command);

        // Assert is handled by the mock expectations
    }

    /**
     * @test
     */
    public function should_create_product_without_variants(): void
    {
        // Arrange
        $name = 'Test Product';
        $description = 'Product description';
        $price = 99.99;
        $stock = 10;
        $variants = [];

        $command = new CreateProductCommand($name, $description, $price, $stock);

        $this->repository->expects($this->once())
            ->method('save')
            ->with($this->callback(function(Product $product) use ($name, $description, $price, $stock) {
                return $product->getName()->value() === $name
                    && $product->getDescription() === $description
                    && $product->getPrice()->value() === $price
                    && $product->getStock()->value() === $stock
                    && count($product->getVariants()) === 0;
            }));
            
        $this->eventDispatcher->expects($this->once())
            ->method('dispatch')
            ->with($this->isInstanceOf(ProductCreatedEvent::class));

        // Act
        $this->handler->__invoke($command);

        // Assert is handled by the mock expectations
    }
}
