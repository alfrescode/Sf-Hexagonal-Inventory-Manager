<?php

declare(strict_types=1);

namespace App\Domain\Product;

interface ProductRepositoryInterface
{
    public function save(Product $product): void;
    public function findById(string $id): ?Product;
    public function delete(Product $product): void;
    public function findAll(): array;
}