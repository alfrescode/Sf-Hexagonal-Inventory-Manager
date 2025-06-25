<?php

namespace App\Application\Event;

use App\Domain\Product\Event\ProductDeletedEvent;
use App\Infrastructure\Email\Contract\EmailSenderInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Escucha los eventos de eliminación de productos y ejecuta acciones en respuesta.
 */
class ProductDeletedListener implements EventSubscriberInterface
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

        // Enviar correo de notificación
        $subject = 'Producto eliminado del inventario';
        
        $body = sprintf(
            '<h1>Se ha eliminado un producto</h1>
            <p>El producto con ID <strong>%s</strong> ha sido eliminado del sistema.</p>
            <p>Esta es una notificación automática. Por favor, no responda a este correo.</p>',
            $event->getProductId()
        );
        
        $this->emailSender->send('pepe@up-spain.com', $subject, $body);
    }
}
