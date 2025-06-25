<?php

namespace App\Infrastructure\Email;

use App\Infrastructure\Email\Contract\EmailSenderInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Part\DataPart;
use App\Infrastructure\Persistence\Doctrine\Entity\EmailLog;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Implementación del servicio de correo electrónico utilizando el componente Mailer de Symfony.
 */
class SymfonyMailer implements EmailSenderInterface
{
    private MailerInterface $mailer;
    private EntityManagerInterface $entityManager;
    private string $fromEmail;
    private string $fromName;

    /**
     * @param MailerInterface $mailer Servicio Mailer de Symfony
     * @param EntityManagerInterface $entityManager
     * @param string $fromEmail Dirección de correo del remitente
     * @param string $fromName Nombre del remitente
     */
    public function __construct(
        MailerInterface $mailer,
        EntityManagerInterface $entityManager,
        string $fromEmail = 'noreply@example.com',
        string $fromName = 'Sistema de Inventario'
    ) {
        $this->mailer = $mailer;
        $this->entityManager = $entityManager;
        $this->fromEmail = $fromEmail;
        $this->fromName = $fromName;
    }

    /**
     * {@inheritdoc}
     */
    public function send(string $to, string $subject, string $body, array $attachments = []): bool
    {
        try {
            $email = (new Email())
                ->from(sprintf('%s <%s>', $this->fromName, $this->fromEmail))
                ->to($to)
                ->subject($subject)
                ->html($body);

            // Agregar archivos adjuntos si existen
            foreach ($attachments as $name => $path) {
                $email->addPart(new DataPart(fopen($path, 'r'), $name));
            }

            // Enviamos el email usando el mailer configurado
            $this->mailer->send($email);

            // Registrar el correo en la base de datos
            $emailLog = new EmailLog($to, $subject, $body);
            $this->entityManager->persist($emailLog);
            $this->entityManager->flush();

            // En desarrollo, guardamos una copia del correo en un archivo para visualización
            if ($_ENV['APP_ENV'] === 'dev') {
                $this->saveEmailToFile($email, $to, $subject, $body);
            }

            return true;
        } catch (\Exception $e) {
            // Aquí se podría agregar un log del error
            return false;
        }
    }

    /**
     * Guarda una copia del correo electrónico en un archivo para visualización en desarrollo
     */
    private function saveEmailToFile(Email $email, string $to, string $subject, string $body): void
    {
        $projectDir = dirname(__DIR__, 3); // Subimos tres niveles desde Infrastructure/Email
        $emailDir = $projectDir . '/var/email';

        // Crear el directorio si no existe
        if (!is_dir($emailDir)) {
            mkdir($emailDir, 0777, true);
        }

        // Crear un nombre de archivo único
        $filename = date('Y-m-d_H-i-s') . '_' . md5($to . $subject . time()) . '.email';
        $filepath = $emailDir . '/' . $filename;

        // Preparar el contenido del archivo
        $content = "De: {$this->fromName} <{$this->fromEmail}>\n";
        $content .= "Para: {$to}\n";
        $content .= "Asunto: {$subject}\n";
        $content .= "Fecha: " . date('Y-m-d H:i:s') . "\n";
        $content .= "Contenido HTML:\n\n";
        $content .= $body;

        // Guardar el archivo
        file_put_contents($filepath, $content);
        chmod($filepath, 0666); // Asegurar que sea legible
    }
}
