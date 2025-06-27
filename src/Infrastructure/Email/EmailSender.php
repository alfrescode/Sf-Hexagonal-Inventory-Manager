<?php

namespace App\Infrastructure\Email;

use App\Domain\Product\Contract\EmailSenderInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

class EmailSender implements EmailSenderInterface
{
    private MailerInterface $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function send(string $to, string $subject, string $body): bool
    {
        try {
            $email = (new Email())
                ->from('noreply@example.com')
                ->to($to)
                ->subject($subject)
                ->html($body);

            $this->mailer->send($email);
            return true;
        } catch (TransportExceptionInterface $e) {
            return false;
        }
    }
}
