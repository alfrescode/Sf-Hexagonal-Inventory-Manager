<?php

namespace App\Application\Event;

use App\Domain\Product\Event\ProductUpdatedEvent;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Escucha los eventos de actualización de productos y ejecuta acciones en respuesta.
 */
class ProductUpdatedListener implements EventSubscriberInterface
{
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ProductUpdatedEvent::class => 'onProductUpdated',
        ];
    }

    /**
     * Maneja el evento de actualización de producto.
     *
     * @param ProductUpdatedEvent $event
     */
    public function onProductUpdated(ProductUpdatedEvent $event): void
    {
        $product = $event->getProduct();
        
        // Registrar la actualización del producto
        $this->logger->info(
            'Producto actualizado',
            [
                'id' => $product->getId()->value(),
                'name' => $product->getName()->value(),
                'price' => $product->getPrice()->value(),
                'stock' => $product->getStock()->value(),
            ]
        );

        // Aquí se pueden agregar más acciones como enviar emails, notificaciones, etc.
    }
}
