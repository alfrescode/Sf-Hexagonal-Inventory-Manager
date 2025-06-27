<?php

namespace App\Application\Event;

use Psr\Log\LoggerInterface;
use App\Domain\Product\Event\ProductDeletedEvent;

/**
 * Escucha los eventos de eliminación de productos y ejecuta acciones en respuesta.
 */
class ProductDeletedListener
{
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Maneja el evento de eliminación de producto.
     *
     * @param ProductDeletedEvent $event
     */
    public function onProductDeleted(ProductDeletedEvent $event): void
    {
        // Registrar la eliminación del producto
        $this->logger->info('Product deleted: ' . $event->getProductId());
    }
}
