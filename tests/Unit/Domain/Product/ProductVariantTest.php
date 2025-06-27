<?php

namespace App\Tests\Unit\Domain\Product;

use App\Domain\Product\ProductVariant;
use App\Domain\Product\ValueObject\ProductPrice;
use App\Domain\Product\ValueObject\ProductStock;
use PHPUnit\Framework\TestCase;

class ProductVariantTest extends TestCase
{
    private string $size;
    private string $color;
    private ProductPrice $price;
    private ProductStock $stock;
    private string $imageUrl;

    protected function setUp(): void
    {
        $this->size = 'M';
        $this->color = 'Red';
        $this->price = new ProductPrice(89.99);
        $this->stock = new ProductStock(5);
        $this->imageUrl = 'http://example.com/image.jpg';
    }

    /**
     * @test
     */
    public function should_create_product_variant_with_valid_values(): void
    {
        // Act
        $variant = new ProductVariant(
            $this->size,
            $this->color,
            $this->price,
            $this->stock,
            $this->imageUrl
        );
        
        // Assert
        $this->assertEquals($this->size, $variant->getSize());
        $this->assertEquals($this->color, $variant->getColor());
        $this->assertSame($this->price, $variant->getPrice());
        $this->assertSame($this->stock, $variant->getStock());
        $this->assertEquals($this->imageUrl, $variant->getImageUrl());
    }
    
    /**
     * @test
     */
    public function should_update_product_variant_properties(): void
    {
        // Arrange
        $variant = new ProductVariant(
            $this->size,
            $this->color,
            $this->price,
            $this->stock,
            $this->imageUrl
        );
        
        $newSize = 'L';
        $newColor = 'Blue';
        $newPrice = new ProductPrice(99.99);
        $newStock = new ProductStock(10);
        $newImageUrl = 'http://example.com/updated.jpg';
        
        // Act
        $variant->setSize($newSize);
        $variant->setColor($newColor);
        $variant->setPrice($newPrice);
        $variant->setStock($newStock);
        $variant->setImageUrl($newImageUrl);
        
        // Assert
        $this->assertEquals($newSize, $variant->getSize());
        $this->assertEquals($newColor, $variant->getColor());
        $this->assertSame($newPrice, $variant->getPrice());
        $this->assertSame($newStock, $variant->getStock());
        $this->assertEquals($newImageUrl, $variant->getImageUrl());
    }
}
