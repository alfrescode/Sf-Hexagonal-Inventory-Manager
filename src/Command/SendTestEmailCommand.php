<?php

namespace App\Command;

use App\Infrastructure\Email\Contract\EmailSenderInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:send-test-email',
    description: 'Envía un correo electrónico de prueba',
)]
class SendTestEmailCommand extends Command
{
    private EmailSenderInterface $emailSender;

    public function __construct(EmailSenderInterface $emailSender)
    {
        parent::__construct();
        $this->emailSender = $emailSender;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->title('Enviando correo electrónico de prueba');

        try {
            $success = $this->emailSender->send(
                'pepe@up-spain.com',
                'Correo de prueba desde el comando',
                '<h1>Este es un correo de prueba</h1><p>Enviado desde el comando app:send-test-email.</p>'
            );

            if ($success) {
                $io->success('Correo enviado correctamente. Revisa http://localhost:8025 para ver el correo en Mailhog.');
                return Command::SUCCESS;
            }
        } catch (\Exception $e) {
            $io->error('Error al enviar el correo: ' . $e->getMessage());
            return Command::FAILURE;
        }

        $io->error('Error al enviar el correo.');
        return Command::FAILURE;
    }
}
