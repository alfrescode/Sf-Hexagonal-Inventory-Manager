<?php

namespace App\Application\Event;

use Psr\Log\LoggerInterface;
use App\Domain\Product\Event\ProductUpdatedEvent;

class ProductUpdatedListener
{
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function onProductUpdated(ProductUpdatedEvent $event): void
    {
        $this->logger->info('Product updated: ' . $event->getProductId());
    }
}
