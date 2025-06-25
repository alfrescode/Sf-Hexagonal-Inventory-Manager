<?php

namespace App\UI\Controller;

use Doctrine\DBAL\Connection;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\DataCollector\TimeDataCollector;
use Symfony\Component\HttpKernel\Profiler\Profiler;

class ProfilerWebController extends AbstractController
{
    public function __construct(
        private Connection $connection,
        private ?Profiler $profiler
    ) {}

    #[Route('/profiler', name: 'app_profiler')]
    public function profiler(Request $request): Response
    {
        // Iniciar medición de tiempo
        $startTime = microtime(true);

        try {
            // Realizar algunas operaciones de prueba
            $this->performTestOperations();

            // Recopilar datos de rendimiento
            $profilerData = $this->collectProfilerData($request);

            $endTime = microtime(true);
            $executionTime = $endTime - $startTime;

            // Añadir el tiempo de ejecución a los datos del profiler
            $profilerData['execution_time'] = $executionTime;

            return $this->render('profiler/index.html.twig', [
                'profiler_data' => $profilerData ?? []
            ]);
        } catch (\Exception $e) {
            $this->addFlash('error', 'Error: ' . $e->getMessage());
            return $this->render('profiler/index.html.twig', [
                'profiler_data' => [],
            ]);
        }
    }

    private function performTestOperations(): void
    {
        // Simular algunas operaciones de base de datos
        $this->connection->executeQuery('SELECT 1');
        $this->connection->executeQuery('SELECT datetime("now")');

        // Simular una operación que toma tiempo
        usleep(100000); // 100ms

        // Simular una petición HTTP con User-Agent y manejo de errores
        $options = [
            'http' => [
                'header' => "User-Agent: MyApp\r\n"
            ]
        ];
        $context = stream_context_create($options);
        $response = file_get_contents('https://api.github.com/zen', false, $context);

        if ($response === false) {
            throw new \Exception('Error al realizar la solicitud HTTP');
        }

        // Opcional: retornar o usar $response si es necesario
    }

    private function collectProfilerData(Request $request): array
    {
        $data = [
            'execution_time' => 0,
            'sql_queries_count' => 0,
            'http_requests_count' => 0,
            'errors_count' => 0,
            'memory_usage' => memory_get_peak_usage(true) / 1024 / 1024, // En MB
            'php_version' => PHP_VERSION,
            'symfony_environment' => $this->getParameter('kernel.environment'),
        ];

        // Recopilar información de consultas SQL
        if ($this->profiler) {
            $profile = $this->profiler->collect($request, new Response());
            $sqlCollector = $profile->getCollector('db');
            $data['sql_queries_count'] = $sqlCollector->getQueryCount();
            $data['sql_queries_time'] = $sqlCollector->getTime();
        }

        // Contar errores desde los mensajes flash
        $flashBag = $request->getSession()->getFlashBag();
        $data['errors_count'] = count($flashBag->get('error', []));

        return $data;
    }

    #[Route('/profiler/test', name: 'app_profiler_test', methods: ['POST'])]
    public function runTest(): Response
    {
        try {
            $this->performTestOperations();
            $this->addFlash('success', 'Prueba de rendimiento ejecutada correctamente');
        } catch (\Exception $e) {
            $this->addFlash('error', 'Error durante la prueba: ' . $e->getMessage());
        }

        return $this->redirectToRoute('app_profiler');
    }
}
