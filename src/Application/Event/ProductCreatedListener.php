<?php

namespace App\Application\Event;

use App\Domain\Product\Event\ProductCreatedEvent;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Escucha los eventos de creación de productos y ejecuta acciones en respuesta.
 */
class ProductCreatedListener implements EventSubscriberInterface
{
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ProductCreatedEvent::class => 'onProductCreated',
        ];
    }

    /**
     * Maneja el evento de creación de producto.
     *
     * @param ProductCreatedEvent $event
     */
    public function onProductCreated(ProductCreatedEvent $event): void
    {
        $product = $event->getProduct();
        
        // Registrar la creación del producto
        $this->logger->info(
            'Producto creado',
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
