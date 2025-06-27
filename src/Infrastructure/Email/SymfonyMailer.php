<?php

namespace App\Infrastructure\Email;

use App\Infrastructure\Email\Contract\EmailSenderInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

/**
 * Implementación del servicio de correo electrónico utilizando el componente Mailer de Symfony.
 */
class SymfonyMailer implements EmailSenderInterface
{
    private MailerInterface $mailer;

    /**
     * @param MailerInterface $mailer Servicio Mailer de Symfony
     */
    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * {@inheritdoc}
     */
    public function send(string $to, string $subject, string $body, array $attachments = []): bool
    {
        try {
            $email = (new Email())
                ->to($to)
                ->subject($subject)
                ->html($body);

            foreach ($attachments as $attachment) {
                $email->attachFromPath($attachment);
            }

            // Enviamos el email usando el mailer configurado
            $this->mailer->send($email);

            return true;
        } catch (\Exception $e) {
            // Aquí se podría agregar un log del error
            return false;
        }
    }
}
