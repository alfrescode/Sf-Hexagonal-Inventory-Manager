<?php
namespace App\Application\Command\CreateProduct; // Define el espacio de nombres para organizar el código

use App\Domain\Product\Product; // Importa la clase Product
use App\Domain\Product\ProductVariant; // Importa la clase ProductVariant
use App\Domain\Product\ValueObject\ProductId; // Importa el ValueObject ProductId
use App\Domain\Product\ValueObject\ProductName; // Importa el ValueObject ProductName
use App\Domain\Product\ValueObject\ProductPrice; // Importa el ValueObject ProductPrice
use App\Domain\Product\ValueObject\ProductStock; // Importa el ValueObject ProductStock
use App\Domain\Product\Contract\ProductRepositoryInterface; // Importa la interfaz del repositorio de productos
use Symfony\Component\EventDispatcher\EventDispatcherInterface; // Importa la interfaz correcta para el despachador de eventos
use Symfony\Component\Uid\Uuid; // Importa la clase Uuid para generar IDs únicos
use App\Domain\Product\Event\ProductCreatedEvent; // Importa el evento de producto creado

class CreateProductHandler // Define la clase que maneja la creación de productos
{
    private ProductRepositoryInterface $repository; // Repositorio para guardar productos
    private ?EventDispatcherInterface $eventDispatcher;

    public function __construct(
        ProductRepositoryInterface $repository,
        ?EventDispatcherInterface $eventDispatcher = null
    ) {
        $this->repository = $repository;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function __invoke(CreateProductCommand $command): void // Método invocable que maneja el comando
    {
        $product = new Product( // Crea una nueva instancia de Product
            new ProductId(Uuid::v4()->toRfc4122()), // Genera un ID único para el producto
            new ProductName($command->name), // Crea el nombre del producto usando el valor del comando
            $command->description, // Asigna la descripción directamente desde el comando
            new ProductPrice($command->price), // Crea el precio del producto
            new ProductStock($command->stock), // Crea el stock del producto
            array_map(function ($variant) { // Mapea cada variante recibida en el comando a un objeto ProductVariant
                return new ProductVariant(
                    $variant['size'] ?? '', // Toma el tamaño o vacío si no existe
                    $variant['color'] ?? '', // Toma el color o vacío si no existe
                    new ProductPrice($variant['price']), // Crea el precio de la variante
                    new ProductStock($variant['stock']), // Crea el stock de la variante
                    $variant['imageUrl'] ?? '' // Toma la URL de la imagen o vacío si no existe
                );
            }, $command->variants) // Aplica la función a cada variante
        );

        $this->repository->save($product); // Guarda el producto en el repositorio

        // Lanzar evento de dominio
        if ($this->eventDispatcher) {
            $this->eventDispatcher->dispatch(new ProductCreatedEvent($product));
        }
    }
}