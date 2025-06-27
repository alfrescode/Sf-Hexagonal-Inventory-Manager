<?php

namespace App\UI\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Infrastructure\Email\Contract\EmailSenderInterface;

class EmailTestController extends AbstractController
{
    #[Route('/email-test', name: 'app_email_test')]
    public function index(): Response
    {
        $emailDir = $this->getParameter('kernel.project_dir') . '/var/email';
        $emails = [];
        
        if (is_dir($emailDir)) {
            $files = scandir($emailDir);
            foreach ($files as $file) {
                if ($file !== '.' && $file !== '..' && is_file($emailDir . '/' . $file)) {
                    $emails[] = [
                        'filename' => $file,
                        'content' => file_get_contents($emailDir . '/' . $file),
                        'date' => date('Y-m-d H:i:s', filemtime($emailDir . '/' . $file))
                    ];
                }
            }
            
            // Ordenar por fecha más reciente primero
            usort($emails, function($a, $b) {
                return strtotime($b['date']) - strtotime($a['date']);
            });
        }
        
        return $this->render('email_test/index.html.twig', [
            'emails' => $emails,
        ]);
    }
    
    #[Route('/email-test/send', name: 'app_email_test_send', methods: ['POST'])]
    public function send(Request $request, EmailSenderInterface $emailSender): Response
    {
        $email = $request->request->get('email');
        $subject = $request->request->get('subject');
        $message = $request->request->get('message');
        
        // Validación básica
        if (!$email || !$subject || !$message) {
            $this->addFlash('danger', 'Por favor, complete todos los campos del formulario.');
            return $this->redirectToRoute('app_email_test');
        }
        
        // Validar formato de email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->addFlash('danger', 'Por favor, ingrese una dirección de correo válida.');
            return $this->redirectToRoute('app_email_test');
        }
        
        // Convertir el mensaje de texto plano a HTML básico
        $htmlMessage = nl2br(htmlspecialchars($message));
        
        $success = $emailSender->send(
            $email,
            $subject,
            $htmlMessage
        );
        
        $this->addFlash(
            $success ? 'success' : 'danger',
            $success ? 'Correo enviado correctamente.' : 'Error al enviar el correo.'
        );
        
        return $this->redirectToRoute('app_email_test');
    }
    
    #[Route('/email-test/clear', name: 'app_email_test_clear')]
    public function clear(): Response
    {
        $emailDir = $this->getParameter('kernel.project_dir') . '/var/email';
        
        if (is_dir($emailDir)) {
            $files = scandir($emailDir);
            foreach ($files as $file) {
                if ($file !== '.' && $file !== '..' && is_file($emailDir . '/' . $file)) {
                    unlink($emailDir . '/' . $file);
                }
            }
        }
        
        $this->addFlash('success', 'Todos los correos han sido eliminados.');
        
        return $this->redirectToRoute('app_email_test');
    }
}
