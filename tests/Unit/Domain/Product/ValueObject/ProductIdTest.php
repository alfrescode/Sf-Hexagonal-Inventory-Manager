<?php

namespace App\Tests\Unit\Domain\Product\ValueObject;

use App\Domain\Product\ValueObject\ProductId;
use PHPUnit\Framework\TestCase;

class ProductIdTest extends TestCase
{
    /**
     * @test
     */
    public function should_create_product_id_with_valid_value(): void
    {
        // Arrange
        $idValue = 'product-123';
        
        // Act
        $productId = new ProductId($idValue);
        
        // Assert
        $this->assertEquals($idValue, $productId->value());
    }
    
    /**
     * @test
     */
    public function should_convert_to_string(): void
    {
        // Arrange
        $idValue = 'product-123';
        $productId = new ProductId($idValue);
        
        // Act
        $result = (string)$productId;
        
        // Assert
        $this->assertEquals($idValue, $result);
    }
}
