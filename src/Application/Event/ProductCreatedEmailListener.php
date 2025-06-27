<?php

namespace App\Application\Event;

use App\Domain\Product\Contract\EmailSenderInterface;
use App\Domain\Product\Event\ProductCreatedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ProductCreatedEmailListener implements EventSubscriberInterface
{
    private EmailSenderInterface $emailSender;

    public function __construct(EmailSenderInterface $emailSender)
    {
        $this->emailSender = $emailSender;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ProductCreatedEvent::class => 'onProductCreated'
        ];
    }

    public function onProductCreated(ProductCreatedEvent $event): void
    {
        $product = $event->getProduct();
        $subject = "Nuevo producto creado: " . $product->getName()->value();
        
        $body = "<h1>Se ha creado un nuevo producto</h1>";
        $body .= "<p><strong>Nombre:</strong> " . $product->getName()->value() . "</p>";
        $body .= "<p><strong>Descripción:</strong> " . $product->getDescription() . "</p>";
        $body .= "<p><strong>Precio:</strong> " . $product->getPrice()->value() . "€</p>";
        $body .= "<p><strong>Stock inicial:</strong> " . $product->getStock()->value() . " unidades</p>";

        if (count($product->getVariants()) > 0) {
            $body .= "<h2>Variantes:</h2><ul>";
            foreach ($product->getVariants() as $variant) {
                $body .= "<li>";
                $body .= "Talla: " . $variant->getSize() . ", ";
                $body .= "Color: " . $variant->getColor() . ", ";
                $body .= "Precio: " . $variant->getPrice()->value() . "€, ";
                $body .= "Stock: " . $variant->getStock()->value() . " unidades";
                $body .= "</li>";
            }
            $body .= "</ul>";
        }

        $this->emailSender->send('pepe@up-spain.com', $subject, $body);
    }
}
