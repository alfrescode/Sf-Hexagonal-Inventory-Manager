<?php
namespace App\Infrastructure\Listener;

use App\Domain\Product\Event\ProductCreatedEvent;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class ProductCreatedEmailListener
{
    private MailerInterface $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function onProductCreated(ProductCreatedEvent $event): void
    {
        $product = $event->getProduct();

        $email = (new Email())
            ->from('no-reply@inventory-manager.com')
            ->to('pepe@up-spain.com')
            ->subject('Nuevo producto creado')
            ->html(sprintf(
                '<p>Se ha creado un nuevo producto:</p>
                <ul>
                    <li><strong>Nombre:</strong> %s</li>
                    <li><strong>Descripción:</strong> %s</li>
                    <li><strong>Precio:</strong> %.2f</li>
                    <li><strong>Stock:</strong> %d</li>
                </ul>',
                $product->getName(),
                $product->getDescription(),
                $product->getPrice(),
                $product->getStock()
            ));

        $this->mailer->send($email);
    }
}