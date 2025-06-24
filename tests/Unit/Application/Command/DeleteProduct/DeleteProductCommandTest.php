<?php

namespace App\Tests\Unit\Application\Command\DeleteProduct;

use App\Application\Command\DeleteProduct\DeleteProductCommand;
use PHPUnit\Framework\TestCase;

class DeleteProductCommandTest extends TestCase
{
    /**
     * @test
     */
    public function should_create_delete_product_command_with_id(): void
    {
        // Arrange
        $productId = 'product-123';

        // Act
        $command = new DeleteProductCommand($productId);

        // Assert
        $this->assertEquals($productId, $command->id);
    }
}
