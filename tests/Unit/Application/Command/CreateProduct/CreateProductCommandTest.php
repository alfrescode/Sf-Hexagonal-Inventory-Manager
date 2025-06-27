<?php

namespace App\Tests\Unit\Application\Command\CreateProduct;

use App\Application\Command\CreateProduct\CreateProductCommand;
use PHPUnit\Framework\TestCase;

class CreateProductCommandTest extends TestCase
{
    /**
     * @test
     */
    public function should_create_command_with_required_properties(): void
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

        // Act
        $command = new CreateProductCommand($name, $description, $price, $stock, $variants);

        // Assert
        $this->assertEquals($name, $command->name);
        $this->assertEquals($description, $command->description);
        $this->assertEquals($price, $command->price);
        $this->assertEquals($stock, $command->stock);
        $this->assertEquals($variants, $command->variants);
    }

    /**
     * @test
     */
    public function should_create_command_with_empty_variants(): void
    {
        // Arrange
        $name = 'Test Product';
        $description = 'Product description';
        $price = 99.99;
        $stock = 10;

        // Act
        $command = new CreateProductCommand($name, $description, $price, $stock);

        // Assert
        $this->assertEquals($name, $command->name);
        $this->assertEquals($description, $command->description);
        $this->assertEquals($price, $command->price);
        $this->assertEquals($stock, $command->stock);
        $this->assertEquals([], $command->variants);
    }
}
