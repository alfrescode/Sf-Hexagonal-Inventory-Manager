<?php

namespace App\Tests\Unit\Application\Command\UpdateProduct;

use App\Application\Command\UpdateProduct\UpdateProductCommand;
use PHPUnit\Framework\TestCase;

class UpdateProductCommandTest extends TestCase
{
    /**
     * @test
     */
    public function should_create_command_with_all_properties(): void
    {
        // Arrange
        $id = '123';
        $name = 'Updated Product';
        $description = 'Updated description';
        $price = 199.99;
        $stock = 20;
        $variants = [
            [
                'size' => 'L',
                'color' => 'Blue',
                'price' => 189.99,
                'stock' => 15,
                'imageUrl' => 'http://example.com/updated.jpg'
            ]
        ];

        // Act
        $command = new UpdateProductCommand($id, $name, $description, $price, $stock, $variants);

        // Assert
        $this->assertEquals($id, $command->id);
        $this->assertEquals($name, $command->name);
        $this->assertEquals($description, $command->description);
        $this->assertEquals($price, $command->price);
        $this->assertEquals($stock, $command->stock);
        $this->assertEquals($variants, $command->variants);
    }

    /**
     * @test
     */
    public function should_create_command_with_partial_properties(): void
    {
        // Arrange
        $id = '123';
        $name = 'Updated Product';
        $price = 199.99;

        // Act
        $command = new UpdateProductCommand($id, $name, null, $price);

        // Assert
        $this->assertEquals($id, $command->id);
        $this->assertEquals($name, $command->name);
        $this->assertNull($command->description);
        $this->assertEquals($price, $command->price);
        $this->assertNull($command->stock);
        $this->assertNull($command->variants);
    }

    /**
     * @test
     */
    public function should_create_command_with_only_id(): void
    {
        // Arrange
        $id = '123';

        // Act
        $command = new UpdateProductCommand($id);

        // Assert
        $this->assertEquals($id, $command->id);
        $this->assertNull($command->name);
        $this->assertNull($command->description);
        $this->assertNull($command->price);
        $this->assertNull($command->stock);
        $this->assertNull($command->variants);
    }
}
