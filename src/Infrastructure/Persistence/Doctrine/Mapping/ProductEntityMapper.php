<?php
namespace App\Infrastructure\Persistence\Doctrine\Mapping;

use App\Domain\Product\Product;
use App\Domain\Product\ProductVariant;
use App\Domain\Product\ValueObject\ProductId;
use App\Domain\Product\ValueObject\ProductName;
use App\Domain\Product\ValueObject\ProductPrice;
use App\Domain\Product\ValueObject\ProductStock;
use App\Infrastructure\Persistence\Doctrine\Entity\ProductEntity;
use App\Infrastructure\Persistence\Doctrine\Entity\ProductVariantEntity;

class ProductEntityMapper
{
    public static function toEntity(Product $product): ProductEntity
    {
        $entity = new ProductEntity(
            $product->getId()->value(),
            $product->getName()->value(),
            $product->getPrice()->value(),
            $product->getStock()->value(),
            $product->getDescription()
        );

        foreach ($product->getVariants() as $variant) {
            $variantEntity = new ProductVariantEntity(
                $variant->getSize(),
                $variant->getColor(),
                $variant->getPrice()->value(),
                $variant->getStock()->value(),
                $variant->getImageUrl(),
                $entity
            );
            $entity->getVariants()->add($variantEntity);
        }

        return $entity;
    }

    public static function toDomain(ProductEntity $entity): Product
    {
        $variants = [];
        foreach ($entity->getVariants() as $variantEntity) {
            $variants[] = new ProductVariant(
                $variantEntity->getSize(),
                $variantEntity->getColor(),
                new ProductPrice($variantEntity->getPrice()),
                new ProductStock($variantEntity->getStock()),
                $variantEntity->getImageUrl()
            );
        }
        return new Product(
            new ProductId($entity->getId()),
            new ProductName($entity->getName()),
            $entity->getDescription(),
            new ProductPrice($entity->getPrice()),
            new ProductStock($entity->getStock()),
            $variants
        );
    }
}