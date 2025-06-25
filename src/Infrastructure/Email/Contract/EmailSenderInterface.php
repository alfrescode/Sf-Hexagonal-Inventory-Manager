<?php

namespace App\Infrastructure\Email\Contract;

/**
 * Interface para servicios de envío de correo electrónico.
 */
interface EmailSenderInterface
{
    /**
     * Envía un correo electrónico.
     *
     * @param string $to Dirección de correo electrónico del destinatario
     * @param string $subject Asunto del correo
     * @param string $body Cuerpo del correo (puede ser HTML)
     * @param array $attachments Archivos adjuntos (opcional)
     * @return bool Éxito o fracaso del envío
     */
    public function send(string $to, string $subject, string $body, array $attachments = []): bool;
}
