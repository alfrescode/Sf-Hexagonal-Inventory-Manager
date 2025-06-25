<?php

namespace App\UI\Controller;

use App\Infrastructure\Email\Contract\EmailSenderInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SimpleEmailTestController extends AbstractController
{
    #[Route('/simple-email-test', name: 'app_simple_email_test')]
    public function index(EmailSenderInterface $emailSender): Response
    {
        $success = $emailSender->send(
            'pepe@up-spain.com',
            'Correo de prueba simple',
            '<h1>Este es un correo de prueba simple</h1><p>Enviado desde el controlador SimpleEmailTestController.</p>'
        );

        return $this->render('simple_email_test/index.html.twig', [
            'success' => $success,
        ]);
    }
}
