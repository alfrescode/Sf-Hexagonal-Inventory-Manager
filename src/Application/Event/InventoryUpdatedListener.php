<?php

namespace App\Application\Event;

use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Escucha los eventos de actualización de inventario y ejecuta acciones en respuesta
 */
class InventoryUpdatedListener implements EventSubscriberInterface
{
    private LoggerInterface $logger;
    
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }
    
    public static function getSubscribedEvents(): array
    {
        return [
            InventoryUpdatedEvent::class => 'onInventoryUpdated',
        ];
    }
    
    /**
     * Maneja el evento de actualización de inventario
     */
    public function onInventoryUpdated(InventoryUpdatedEvent $event): void
    {
        // Registrar la actualización de inventario
        $this->logger->info(
            'Inventario actualizado',
            [
                'productId' => $event->getProductId(),
                'quantityChange' => $event->getQuantityChange(),
                'reason' => $event->getReason(),
            ]
        );
        
        // Aquí podrían implementarse otras acciones como:
        // - Enviar notificaciones cuando el stock es bajo
        // - Actualizar estadísticas de inventario
        // - Generar reportes automáticos
    }
}
