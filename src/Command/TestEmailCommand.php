<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Twig\Environment;

#[AsCommand(
    name: 'app:send-test-email',
    description: 'Send a test email'
)]
class TestEmailCommand extends Command
{
    public function __construct(
        private MailerInterface $mailer,
        private Environment $twig
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $html = $this->twig->render('email/test_email.html.twig');
        
        $email = (new Email())
            ->from('noreply@example.com')
            ->to('pepe@up-spain.com')
            ->subject('Test email')
            ->html($html);

        $this->mailer->send($email);

        $output->writeln('Test email sent successfully!');

        return Command::SUCCESS;
    }
}
