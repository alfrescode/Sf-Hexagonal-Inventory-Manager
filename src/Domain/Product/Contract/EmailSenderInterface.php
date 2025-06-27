<?php

namespace App\Domain\Product\Contract;

interface EmailSenderInterface
{
    public function send(string $to, string $subject, string $body): bool;
}
