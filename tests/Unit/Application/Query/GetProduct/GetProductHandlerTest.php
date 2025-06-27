<?php

namespace App\Tests\Unit\Application\Query\GetProduct;

use App\Application\Query\GetProduct\GetProductHandler;
use App\Application\Query\GetProduct\GetProductQuery;
use App\Application\Query\GetProduct\ProductDTO;
use App\Application\Query\GetProduct\ProductVariantDTO;
use App\Domain\Product\Contract\ProductRepositoryInterface;
use App\Domain\Product\Exception\ProductNotFoundException;
use App\Domain\Product\Product;
use App\Domain\Product\ProductVariant;
use App\Domain\Product\ValueObject\ProductId;
use App\Domain\Product\ValueObject\ProductName;
use App\Domain\Product\ValueObject\ProductPrice;
use App\Domain\Product\ValueObject\ProductStock;
use PHPUnit\Framework\TestCase;

class GetProductHandlerTest extends TestCase
{
    private ProductRepositoryInterface $repository;
    private GetProductHandler $handler;

    protected function setUp(): void
    {
        $this->repository = $this->createMock(ProductRepositoryInterface::class);
        $this->handler = new GetProductHandler($this->repository);
    }

    /**
     * @test
     */
    public function should_return_product_dto_when_product_exists(): void
    {
        // Arrange
        $productId = 'product-123';
        $query = new GetProductQuery($productId);
        
        $variant = new ProductVariant(
            'M',
            'Red',
            new ProductPrice(89.99),
            new ProductStock(5),
            'http://example.com/image.jpg'
        );
        
        $product = new Product(
            new ProductId($productId),
            new ProductName('Test Product'),
            'Test Description',
            new ProductPrice(99.99),
            new ProductStock(10),
            [$variant]
        );
        
        $this->repository->expects($this->once())
            ->method('find')
            ->with($this->callback(function(ProductId $id) use ($productId) {
                return $id->value() === $productId;
            }))
            ->willReturn($product);

        // Act
        $result = $this->handler->__invoke($query);

        // Assert
        $this->assertInstanceOf(ProductDTO::class, $result);
        $this->assertEquals($productId, $result->id);
        $this->assertEquals('Test Product', $result->name);
        $this->assertEquals('Test Description', $result->description);
        $this->assertEquals(99.99, $result->price);
        $this->assertEquals(10, $result->stock);
        $this->assertCount(1, $result->variants);
        
        $variantDTO = $result->variants[0];
        $this->assertInstanceOf(ProductVariantDTO::class, $variantDTO);
        $this->assertEquals('M', $variantDTO->size);
        $this->assertEquals('Red', $variantDTO->color);
        $this->assertEquals(89.99, $variantDTO->price);
        $this->assertEquals(5, $variantDTO->stock);
        $this->assertEquals('http://example.com/image.jpg', $variantDTO->imageUrl);
    }

    /**
     * @test
     */
    public function should_throw_exception_when_product_not_found(): void
    {
        // Arrange
        $productId = 'non-existent-product';
        $query = new GetProductQuery($productId);
        
        $this->repository->expects($this->once())
            ->method('find')
            ->willReturn(null);

        // Assert
        $this->expectException(ProductNotFoundException::class);
        
        // Act
        $this->handler->__invoke($query);
    }
}
