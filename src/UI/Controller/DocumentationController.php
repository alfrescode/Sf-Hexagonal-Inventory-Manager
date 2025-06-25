<?php

namespace App\UI\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Controlador para gestionar la documentación
 */
#[Route('/docs')]
class DocumentationController extends AbstractController
{
    private KernelInterface $kernel;
    
    public function __construct(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }
    
    /**
     * Muestra el índice de documentación
     */
    #[Route('/', name: 'app_documentation')]
    public function index(): Response
    {
        $docsDir = $this->kernel->getProjectDir() . '/docs';
        
        $markdownFiles = [];
        if (is_dir($docsDir)) {
            $files = scandir($docsDir);
            foreach ($files as $file) {
                if (pathinfo($file, PATHINFO_EXTENSION) === 'md') {
                    $name = pathinfo($file, PATHINFO_FILENAME);
                    $markdownFiles[] = [
                        'name' => str_replace('_', ' ', $name),
                        'filename' => $file
                    ];
                }
            }
        }
        
        return $this->render('documentation/index.html.twig', [
            'markdownFiles' => $markdownFiles
        ]);
    }
    
    /**
     * Muestra un archivo de documentación específico
     */
    #[Route('/{filename}', name: 'app_documentation_show')]
    public function show(string $filename): Response
    {
        $docsDir = $this->kernel->getProjectDir() . '/docs';
        $filePath = $docsDir . '/' . $filename;
        
        if (!file_exists($filePath)) {
            $this->addFlash('error', 'El archivo de documentación no existe');
            return $this->redirectToRoute('app_documentation');
        }
        
        $content = file_get_contents($filePath);
        $title = pathinfo($filename, PATHINFO_FILENAME);
        $title = str_replace('_', ' ', $title);
        
        return $this->render('documentation/show.html.twig', [
            'title' => $title,
            'content' => $content,
            'filename' => $filename
        ]);
    }
}
