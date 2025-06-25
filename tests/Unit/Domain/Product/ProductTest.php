<?php

namespace App\Tests\Unit\Domain\Product;

use App\Domain\Product\Product;
use App\Domain\Product\ProductVariant;
use App\Domain\Product\ValueObject\ProductId;
use App\Domain\Product\ValueObject\ProductName;
use App\Domain\Product\ValueObject\ProductPrice;
use App\Domain\Product\ValueObject\ProductStock;
use PHPUnit\Framework\TestCase;

class ProductTest extends TestCase
{
    private ProductId $id;
    private ProductName $name;
    private string $description;
    private ProductPrice $price;
    private ProductStock $stock;
    private array $variants;

    protected function setUp(): void
    {
        $this->id = new ProductId('product-123');
        $this->name = new ProductName('Test Product');
        $this->description = 'Test Description';
        $this->price = new ProductPrice(99.99);
        $this->stock = new ProductStock(10);
        
        $variant = new ProductVariant(
            'M',
            'Red',
            new ProductPrice(89.99),
            new ProductStock(5),
            'http://example.com/image.jpg'
        );
        
        $this->variants = [$variant];
    }

    /**
     * @test
     */
    public function should_create_product_with_valid_values(): void
    {
        // Act
        $product = new Product(
            $this->id,
            $this->name,
            $this->description,
            $this->price,
            $this->stock,
            $this->variants
        );
        
        // Assert
        $this->assertSame($this->id, $product->getId());
        $this->assertSame($this->name, $product->getName());
        $this->assertEquals($this->description, $product->getDescription());
        $this->assertSame($this->price, $product->getPrice());
        $this->assertSame($this->stock, $product->getStock());
        $this->assertEquals($this->variants, $product->getVariants());
    }
    
    /**
     * @test
     */
    public function should_update_product_properties(): void
    {
        // Arrange
        $product = new Product(
            $this->id,
            $this->name,
            $this->description,
            $this->price,
            $this->stock,
            $this->variants
        );
        
        $newName = new ProductName('Updated Product');
        $newDescription = 'Updated Description';
        $newPrice = new ProductPrice(149.99);
        $newStock = new ProductStock(20);
        
        $newVariant = new ProductVariant(
            'L',
            'Blue',
            new ProductPrice(139.99),
            new ProductStock(15),
            'http://example.com/updated.jpg'
        );
        
        $newVariants = [$newVariant];
        
        // Act
        $product->setName($newName);
        $product->setDescription($newDescription);
        $product->setPrice($newPrice);
        $product->setStock($newStock);
        $product->setVariants($newVariants);
        
        // Assert
        $this->assertSame($newName, $product->getName());
        $this->assertEquals($newDescription, $product->getDescription());
        $this->assertSame($newPrice, $product->getPrice());
        $this->assertSame($newStock, $product->getStock());
        $this->assertEquals($newVariants, $product->getVariants());
    }
    
    /**
     * @test
     */
    public function should_create_product_without_variants(): void
    {
        // Act
        $product = new Product(
            $this->id,
            $this->name,
            $this->description,
            $this->price,
            $this->stock
        );
        
        // Assert
        $this->assertEmpty($product->getVariants());
    }
}
