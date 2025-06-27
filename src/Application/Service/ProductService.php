<?php
namespace App\Application\Service;

use App\Domain\Product\Product;
use App\Domain\Product\Event\ProductCreatedEvent;
use App\Domain\Product\Contract\ProductRepositoryInterface;
use App\Domain\Product\ValueObject\ProductId;
use App\Domain\Product\ValueObject\ProductName;
use App\Domain\Product\ValueObject\ProductDescription;
use App\Domain\Product\ValueObject\ProductPrice;
use App\Domain\Product\ValueObject\ProductStock;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ProductService
{
    private ProductRepositoryInterface $productRepository;
    private EventDispatcherInterface $eventDispatcher;

    public function __construct(
        ProductRepositoryInterface $productRepository,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->productRepository = $productRepository;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function createProduct(string $name, string $description, float $price, int $stock): Product
    {
        // Crear objetos de valor para cada atributo
        $productId = new ProductId();
        $productName = new ProductName($name);
        $productDescription = new ProductDescription($description);
        $productPrice = new ProductPrice($price);
        $productStock = new ProductStock($stock);
        
        // Crear el producto con los objetos de valor
        $product = new Product(
            $productId, 
            $productName, 
            $productDescription, 
            $productPrice, 
            $productStock
        );

        // Guardar en la base de datos
        $this->productRepository->save($product);

        // Disparar evento de dominio
        $event = new ProductCreatedEvent($product);
        $this->eventDispatcher->dispatch($event);

        return $product;
    }
}