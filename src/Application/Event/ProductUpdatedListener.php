<?php

namespace App\Application\Event;

use App\Domain\Product\Event\ProductUpdatedEvent;
use App\Infrastructure\Email\Contract\EmailSenderInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Escucha los eventos de actualización de productos y ejecuta acciones en respuesta.
 */
class ProductUpdatedListener implements EventSubscriberInterface
{
    private LoggerInterface $logger;
    private EmailSenderInterface $emailSender;

    public function __construct(
        LoggerInterface $logger,
        EmailSenderInterface $emailSender
    ) {
        $this->logger = $logger;
        $this->emailSender = $emailSender;
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

        // Enviar correo de notificación
        $subject = 'Producto actualizado: ' . $product->getName()->value();
        
        $body = sprintf(
            '<h1>Se ha actualizado un producto</h1>
            <p>Detalles del producto:</p>
            <ul>
                <li><strong>ID:</strong> %s</li>
                <li><strong>Nombre:</strong> %s</li>
                <li><strong>Descripción:</strong> %s</li>
                <li><strong>Precio:</strong> %.2f</li>
                <li><strong>Stock:</strong> %d</li>
            </ul>',
            $product->getId()->value(),
            $product->getName()->value(),
            $product->getDescription(),
            $product->getPrice()->value(),
            $product->getStock()->value()
        );
        
        $this->emailSender->send('pepe@up-spain.com', $subject, $body);
    }
}
