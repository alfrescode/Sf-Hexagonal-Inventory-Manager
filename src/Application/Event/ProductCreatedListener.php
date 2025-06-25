<?php

namespace App\Application\Event;

use App\Domain\Product\Event\ProductCreatedEvent;
use App\Infrastructure\Email\Contract\EmailSenderInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Escucha los eventos de creación de productos y ejecuta acciones en respuesta.
 */
class ProductCreatedListener implements EventSubscriberInterface
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

        // Enviar correo de notificación
        $subject = 'Nuevo producto creado: ' . $product->getName()->value();
        
        $body = sprintf(
            '<h1>Se ha creado un nuevo producto</h1>
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
