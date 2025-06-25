<?php

namespace App\Infrastructure\Persistence\MongoDB\Repository;

use App\Domain\Product\Contract\ProductMongoRepositoryInterface;
use App\Domain\Product\Product;
use App\Domain\Product\ValueObject\ProductId;
use MongoDB\Client;

class MongoProductRepository implements ProductMongoRepositoryInterface
{
    private $collection;

    public function __construct(string $mongoUrl = 'mongodb://localhost:27017')
    {
        $client = new Client($mongoUrl);
        $this->collection = $client->inventory->products;
    }

    public function save(Product $product): void
    {
        $productData = [
            '_id' => $product->getId()->value(),
            'name' => $product->getName()->value(),
            'description' => $product->getDescription(),
            'price' => $product->getPrice()->value(),
            'stock' => $product->getStock()->value(),
            'variants' => array_map(function ($variant) {
                return [
                    'size' => $variant->getSize(),
                    'color' => $variant->getColor(),
                    'price' => $variant->getPrice()->value(),
                    'stock' => $variant->getStock()->value(),
                    'imageUrl' => $variant->getImageUrl()
                ];
            }, $product->getVariants())
        ];

        $this->collection->updateOne(
            ['_id' => $product->getId()->value()],
            ['$set' => $productData],
            ['upsert' => true]
        );
    }

    public function find(ProductId $id): ?Product
    {
        $document = $this->collection->findOne(['_id' => $id->value()]);
        if (!$document) {
            return null;
        }

        return $this->documentToProduct($document);
    }

    public function findAll(): array
    {
        $cursor = $this->collection->find();
        $products = [];

        foreach ($cursor as $document) {
            $products[] = $this->documentToProduct($document);
        }

        return $products;
    }

    public function delete(ProductId $id): void
    {
        $this->collection->deleteOne(['_id' => $id->value()]);
    }

    private function documentToProduct(array $document): Product
    {
        // Implementar la lógica de conversión de documento MongoDB a Product
        // Similar al ProductEntityMapper pero para documentos MongoDB
    }
}