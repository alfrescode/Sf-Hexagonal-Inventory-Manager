<?php

namespace App\Tests\Unit\Application\Query\ListProducts;

use App\Application\Query\ListProducts\ListProductsHandler;
use App\Application\Query\ListProducts\ListProductsQuery;
use App\Application\Query\ListProducts\ProductsListDTO;
use App\Application\Query\ListProducts\ProductSummaryDTO;
use App\Application\Query\ListProducts\ProductVariantSummaryDTO;
use App\Domain\Product\Contract\ProductRepositoryInterface;
use App\Domain\Product\Product;
use App\Domain\Product\ProductVariant;
use App\Domain\Product\ValueObject\ProductId;
use App\Domain\Product\ValueObject\ProductName;
use App\Domain\Product\ValueObject\ProductPrice;
use App\Domain\Product\ValueObject\ProductStock;
use PHPUnit\Framework\TestCase;

class ListProductsHandlerTest extends TestCase
{
    private ProductRepositoryInterface $repository;
    private ListProductsHandler $handler;

    protected function setUp(): void
    {
        $this->repository = $this->createMock(ProductRepositoryInterface::class);
        $this->handler = new ListProductsHandler($this->repository);
    }

    /**
     * @test
     */
    public function should_return_products_list_dto(): void
    {
        // Arrange
        $query = new ListProductsQuery(1, 10);
        
        $variant1 = new ProductVariant(
            'M',
            'Red',
            new ProductPrice(89.99),
            new ProductStock(5),
            'http://example.com/image1.jpg'
        );
        
        $product1 = new Product(
            new ProductId('product-123'),
            new ProductName('Test Product 1'),
            'Test Description 1',
            new ProductPrice(99.99),
            new ProductStock(10),
            [$variant1]
        );
        
        $variant2 = new ProductVariant(
            'L',
            'Blue',
            new ProductPrice(109.99),
            new ProductStock(3),
            'http://example.com/image2.jpg'
        );
        
        $product2 = new Product(
            new ProductId('product-456'),
            new ProductName('Test Product 2'),
            'Test Description 2',
            new ProductPrice(119.99),
            new ProductStock(8),
            [$variant2]
        );
        
        $products = [$product1, $product2];
        $totalCount = 2;
        
        $this->repository->expects($this->once())
            ->method('findAll')
            ->with(1, 10)
            ->willReturn([$products, $totalCount]);

        // Act
        $result = $this->handler->__invoke($query);

        // Assert
        $this->assertInstanceOf(ProductsListDTO::class, $result);
        $this->assertEquals(2, $result->total);
        $this->assertEquals(1, $result->page);
        $this->assertEquals(10, $result->limit);
        $this->assertCount(2, $result->products);
        
        // Check first product
        $productDTO1 = $result->products[0];
        $this->assertInstanceOf(ProductSummaryDTO::class, $productDTO1);
        $this->assertEquals('product-123', $productDTO1->id);
        $this->assertEquals('Test Product 1', $productDTO1->name);
        $this->assertEquals(99.99, $productDTO1->price);
        $this->assertEquals(10, $productDTO1->stock);
        $this->assertCount(1, $productDTO1->variants);
        
        $variantDTO1 = $productDTO1->variants[0];
        $this->assertInstanceOf(ProductVariantSummaryDTO::class, $variantDTO1);
        $this->assertEquals('M', $variantDTO1->size);
        $this->assertEquals('Red', $variantDTO1->color);
        
        // Check second product
        $productDTO2 = $result->products[1];
        $this->assertInstanceOf(ProductSummaryDTO::class, $productDTO2);
        $this->assertEquals('product-456', $productDTO2->id);
        $this->assertEquals('Test Product 2', $productDTO2->name);
        $this->assertEquals(119.99, $productDTO2->price);
        $this->assertEquals(8, $productDTO2->stock);
        $this->assertCount(1, $productDTO2->variants);
        
        $variantDTO2 = $productDTO2->variants[0];
        $this->assertInstanceOf(ProductVariantSummaryDTO::class, $variantDTO2);
        $this->assertEquals('L', $variantDTO2->size);
        $this->assertEquals('Blue', $variantDTO2->color);
    }

    /**
     * @test
     */
    public function should_return_empty_list_when_no_products(): void
    {
        // Arrange
        $query = new ListProductsQuery(1, 10);
        
        $this->repository->expects($this->once())
            ->method('findAll')
            ->willReturn([[], 0]);

        // Act
        $result = $this->handler->__invoke($query);

        // Assert
        $this->assertInstanceOf(ProductsListDTO::class, $result);
        $this->assertEquals(0, $result->total);
        $this->assertEquals(1, $result->page);
        $this->assertEquals(10, $result->limit);
        $this->assertCount(0, $result->products);
    }
}
