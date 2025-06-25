<?php

namespace App\Tests\Unit\Domain\Product\ValueObject;

use App\Domain\Product\ValueObject\ProductStock;
use PHPUnit\Framework\TestCase;

class ProductStockTest extends TestCase
{
    /**
     * @test
     */
    public function should_create_product_stock_with_valid_value(): void
    {
        // Arrange
        $stockValue = 10;
        
        // Act
        $productStock = new ProductStock($stockValue);
        
        // Assert
        $this->assertEquals($stockValue, $productStock->value());
    }
    
    /**
     * @test
     */
    public function should_create_product_stock_with_zero_value(): void
    {
        // Arrange
        $stockValue = 0;
        
        // Act
        $productStock = new ProductStock($stockValue);
        
        // Assert
        $this->assertEquals($stockValue, $productStock->value());
    }
    
    /**
     * @test
     */
    public function should_throw_exception_when_stock_is_negative(): void
    {
        // Assert
        $this->expectException(\InvalidArgumentException::class);
        
        // Act
        new ProductStock(-5);
    }
}
