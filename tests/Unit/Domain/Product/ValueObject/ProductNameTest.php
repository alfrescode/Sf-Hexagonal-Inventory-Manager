<?php

namespace App\Tests\Unit\Domain\Product\ValueObject;

use App\Domain\Product\ValueObject\ProductName;
use PHPUnit\Framework\TestCase;

class ProductNameTest extends TestCase
{
    /**
     * @test
     */
    public function should_create_product_name_with_valid_value(): void
    {
        // Arrange
        $nameValue = 'Test Product';
        
        // Act
        $productName = new ProductName($nameValue);
        
        // Assert
        $this->assertEquals($nameValue, $productName->value());
    }
    
    /**
     * @test
     */
    public function should_convert_to_string(): void
    {
        // Arrange
        $nameValue = 'Test Product';
        $productName = new ProductName($nameValue);
        
        // Act
        $result = (string)$productName;
        
        // Assert
        $this->assertEquals($nameValue, $result);
    }
    
    /**
     * @test
     */
    public function should_throw_exception_when_name_is_empty(): void
    {
        // Assert
        $this->expectException(\InvalidArgumentException::class);
        
        // Act
        new ProductName('');
    }
}
