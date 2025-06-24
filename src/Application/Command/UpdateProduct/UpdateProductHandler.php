<?php
namespace App\Application\Command\UpdateProduct;

use App\Domain\Product\Product;
use App\Domain\Product\ProductVariant;
use App\Domain\Product\ValueObject\ProductId;
use App\Domain\Product\ValueObject\ProductName;
use App\Domain\Product\ValueObject\ProductPrice;
use App\Domain\Product\ValueObject\ProductStock;
use App\Domain\Product\Event\ProductUpdatedEvent;
use App\Domain\Product\Exception\ProductNotFoundException;
use App\Domain\Product\Contract\ProductRepositoryInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class UpdateProductHandler
{
    private ProductRepositoryInterface $repository;
    private ?EventDispatcherInterface $eventDispatcher;

    public function __construct(
        ProductRepositoryInterface $repository,
        ?EventDispatcherInterface $eventDispatcher = null
    ) {
        $this->repository = $repository;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function __invoke(UpdateProductCommand $command): void
    {
        $product = $this->repository->find(new ProductId($command->id));
        if (!$product) {
            throw new ProductNotFoundException("Producto con ID {$command->id} no encontrado");
        }

        // Solo actualizar los campos que no son nulos
        if ($command->name !== null) {
            $product->setName(new ProductName($command->name));
        }
        if ($command->description !== null) {
            $product->setDescription($command->description);
        }
        if ($command->price !== null) {
            $product->setPrice(new ProductPrice($command->price));
        }
        if ($command->stock !== null) {
            $product->setStock(new ProductStock($command->stock));
        }
        if ($command->variants !== null) {
            $variants = array_map(function ($variant) {
                return new ProductVariant(
                    $variant['size'] ?? '',
                    $variant['color'] ?? '',
                    new ProductPrice($variant['price']),
                    new ProductStock($variant['stock']),
                    $variant['imageUrl'] ?? ''
                );
            }, $command->variants);

            $product->setVariants($variants);
        }

        $this->repository->save($product);

        if ($this->eventDispatcher) {
            $this->eventDispatcher->dispatch(new ProductUpdatedEvent($product));
        }
    }
}