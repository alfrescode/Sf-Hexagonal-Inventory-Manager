<?php
namespace App\Domain\Product\Contract;

use App\Domain\Product\Product;
use App\Domain\Product\ValueObject\ProductId;

interface ProductRepositoryInterface
{
    public function save(Product $product): void;
    public function find(ProductId $id): ?Product;
    public function findAll(int $page = 1, int $limit = 10): array;
    public function delete(ProductId $id): void;
}