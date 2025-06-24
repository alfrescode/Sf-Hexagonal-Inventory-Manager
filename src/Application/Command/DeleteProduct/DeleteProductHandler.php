<?php

namespace App\Application\Command\DeleteProduct;

use App\Domain\Product\Contract\ProductRepositoryInterface;
use App\Domain\Product\Event\ProductDeletedEvent;
use App\Domain\Product\Exception\ProductNotFoundException;
use App\Domain\Product\ValueObject\ProductId;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Manejador para eliminar un producto.
 */
class DeleteProductHandler
{
    private ProductRepositoryInterface $repository;
    private ?EventDispatcherInterface $eventDispatcher;

    /**
     * @param ProductRepositoryInterface $repository Repositorio de productos
     * @param EventDispatcherInterface|null $eventDispatcher Despachador de eventos (opcional)
     */
    public function __construct(
        ProductRepositoryInterface $repository,
        ?EventDispatcherInterface $eventDispatcher = null
    ) {
        $this->repository = $repository;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * Ejecuta el comando para eliminar un producto.
     *
     * @param DeleteProductCommand $command Comando con el ID del producto a eliminar
     * @throws ProductNotFoundException Si el producto no existe
     */
    public function __invoke(DeleteProductCommand $command): void
    {
        // Crear valor de objeto ProductId
        $productId = new ProductId($command->id);
        
        // Verificar si el producto existe
        $product = $this->repository->find($productId);
        if (!$product) {
            throw new ProductNotFoundException("Producto con ID {$command->id} no encontrado");
        }
        
        // Eliminar el producto
        $this->repository->delete($productId);
        
        // Lanzar evento de dominio
        if ($this->eventDispatcher) {
            $this->eventDispatcher->dispatch(new ProductDeletedEvent($command->id));
        }
    }
}
