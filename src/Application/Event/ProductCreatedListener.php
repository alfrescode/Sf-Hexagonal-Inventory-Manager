<?php

namespace App\Application\Event;

use Psr\Log\LoggerInterface;
use App\Domain\Product\Event\ProductCreatedEvent;

class ProductCreatedListener
{
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function onProductCreated(ProductCreatedEvent $event): void
    {
        $this->logger->info('Product created: ' . $event->getProductId());
    }
}
