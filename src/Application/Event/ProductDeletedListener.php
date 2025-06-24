<?php

namespace App\Application\Event;

use App\Domain\Product\Event\ProductDeletedEvent;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Escucha los eventos de eliminación de productos y ejecuta acciones en respuesta.
 */
class ProductDeletedListener implements EventSubscriberInterface
{
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ProductDeletedEvent::class => 'onProductDeleted',
        ];
    }

    /**
     * Maneja el evento de eliminación de producto.
     *
     * @param ProductDeletedEvent $event
     */
    public function onProductDeleted(ProductDeletedEvent $event): void
    {
        // Registrar la eliminación del producto
        $this->logger->info(
            'Producto eliminado',
            [
                'id' => $event->getProductId(),
            ]
        );

        // Aquí se pueden agregar más acciones como enviar emails, notificaciones, etc.
    }
}
