<?php

namespace App\UI\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ConsoleWebController extends AbstractController
{
    #[Route('/console-web', name: 'app_console_web')]
    public function index(): Response
    {
        $commands = [
            'Comandos Composer' => [
                'composer require symfony/form' => 'Instala el componente de formularios de Symfony, necesario para crear y manejar formularios en la aplicación.',
                'composer require symfony/validator' => 'Instala el componente de validación, usado para validar datos de formularios y entidades.',
                'composer require symfony/mailer' => 'Instala el componente de correo electrónico para enviar emails.',
                'composer require doctrine/doctrine-bundle' => 'Instala Doctrine ORM para la gestión de la base de datos.',
                'composer require doctrine/doctrine-migrations-bundle' => 'Instala el sistema de migraciones de base de datos.',
                'composer require erusev/parsedown' => 'Instala la librería Parsedown para convertir Markdown a HTML.',
                'composer require symfony/asset' => 'Instala el componente Asset para gestionar assets web (CSS, JS, imágenes).',
                'composer require symfony/security-bundle' => 'Instala el componente de seguridad para autenticación y autorización.',
            ],
            'Comandos Symfony' => [
                'php bin/console cache:clear' => 'Limpia la caché de la aplicación. Útil después de cambios en la configuración.',
                'php bin/console debug:router' => 'Muestra todas las rutas definidas en la aplicación.',
                'php bin/console make:migration' => 'Genera una nueva migración basada en los cambios en las entidades.',
                'php bin/console doctrine:migrations:migrate' => 'Ejecuta las migraciones pendientes en la base de datos.',
                'php bin/console doctrine:schema:update --force' => 'Actualiza el esquema de la base de datos según las entidades (solo en desarrollo).',
                'php bin/console debug:container' => 'Muestra todos los servicios disponibles en el contenedor de servicios.',
                'php bin/console make:controller' => 'Genera un nuevo controlador.',
                'php bin/console make:entity' => 'Genera una nueva entidad o modifica una existente.',
                'php bin/console make:form' => 'Genera una nueva clase de formulario.',
                'php bin/console server:start' => 'Inicia el servidor web de desarrollo.',
            ],
            'Profiler de Symfony' => [
                'Descripción' => 'El Profiler es una herramienta de desarrollo poderosa que proporciona información detallada sobre cada petición/respuesta.',
                'Características principales' => [
                    'Timeline' => 'Muestra el tiempo de ejecución de cada parte de la aplicación.',
                    'Router' => 'Información sobre la ruta coincidente y otros intentos de coincidencia.',
                    'Security' => 'Detalles sobre el usuario autenticado y los roles.',
                    'Twig' => 'Plantillas renderizadas y tiempo de renderizado.',
                    'Doctrine' => 'Consultas SQL ejecutadas y tiempo de ejecución.',
                    'Email' => 'Correos electrónicos enviados durante la petición.',
                    'Cache' => 'Información sobre el uso de la caché.',
                    'Events' => 'Eventos despachados durante la petición.',
                ],
                'Cómo usar' => 'La barra de depuración web aparece en la parte inferior de tu sitio en el entorno de desarrollo. Haz clic en ella para acceder al Profiler completo.'
            ]
        ];

        return $this->render('console_web/index.html.twig', [
            'commands' => $commands
        ]);
    }
}
