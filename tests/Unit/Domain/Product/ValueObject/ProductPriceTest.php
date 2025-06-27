<?php

namespace App\Tests\Unit\Domain\Product\ValueObject;

use App\Domain\Product\ValueObject\ProductPrice;
use PHPUnit\Framework\TestCase;

class ProductPriceTest extends TestCase
{
    /**
     * @test
     */
    public function should_create_product_price_with_valid_value(): void
    {
        // Arrange
        $priceValue = 99.99;
        
        // Act
        $productPrice = new ProductPrice($priceValue);
        
        // Assert
        $this->assertEquals($priceValue, $productPrice->value());
    }
    
    /**
     * @test
     */
    public function should_create_product_price_with_zero_value(): void
    {
        // Arrange
        $priceValue = 0.0;
        
        // Act
        $productPrice = new ProductPrice($priceValue);
        
        // Assert
        $this->assertEquals($priceValue, $productPrice->value());
    }
    
    /**
     * @test
     */
    public function should_throw_exception_when_price_is_negative(): void
    {
        // Assert
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('El precio no puede ser negativo');
        
        // Act
        new ProductPrice(-10.0);
    }
}
