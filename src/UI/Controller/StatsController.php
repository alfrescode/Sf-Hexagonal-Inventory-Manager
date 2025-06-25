<?php

namespace App\UI\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use App\Domain\Product\Contract\ProductRepositoryInterface;

/**
 * Controlador para estadísticas de productos
 */
#[Route('/api/stats')]
class StatsController extends AbstractController
{
    private ProductRepositoryInterface $productRepository;
    
    public function __construct(ProductRepositoryInterface $productRepository)
    {
        $this->productRepository = $productRepository;
    }
    
    /**
     * Obtiene estadísticas generales de productos
     */
    #[Route('/products', name: 'api_stats_products', methods: ['GET'])]
    public function getProductStats(): JsonResponse
    {
        try {
            [$products, $total] = $this->productRepository->findAll();
            
            // Calcular estadísticas
            $totalStock = 0;
            $totalValue = 0;
            $lowStockCount = 0;
            $categoryCounts = [];
            
            foreach ($products as $product) {
                $stock = $product->getStock()->value();
                $price = $product->getPrice()->value();
                
                $totalStock += $stock;
                $totalValue += ($stock * $price);
                
                if ($stock < 5) {
                    $lowStockCount++;
                }
                
                // Esto es simulado - en un sistema real tendríamos categorías
                $category = "Categoría General";
                if (!isset($categoryCounts[$category])) {
                    $categoryCounts[$category] = 0;
                }
                $categoryCounts[$category]++;
            }
            
            return $this->json([
                'totalProducts' => $total,
                'totalStock' => $totalStock,
                'totalValue' => $totalValue,
                'lowStockCount' => $lowStockCount,
                'categories' => $categoryCounts
            ]);
        } catch (\Exception $e) {
            return $this->json(['error' => 'Error al obtener estadísticas: ' . $e->getMessage()], 500);
        }
    }
    
    /**
     * Obtiene estadísticas de valor de inventario
     */
    #[Route('/inventory-value', name: 'api_stats_inventory_value', methods: ['GET'])]
    public function getInventoryValue(): JsonResponse
    {
        try {
            [$products, $total] = $this->productRepository->findAll();
            
            // Calcular valor por producto
            $productValues = [];
            
            foreach ($products as $product) {
                $stock = $product->getStock()->value();
                $price = $product->getPrice()->value();
                $value = $stock * $price;
                
                $productValues[] = [
                    'id' => $product->getId()->value(),
                    'name' => $product->getName()->value(),
                    'stock' => $stock,
                    'price' => $price,
                    'value' => $value
                ];
            }
            
            // Ordenar por valor descendente
            usort($productValues, function ($a, $b) {
                return $b['value'] <=> $a['value'];
            });
            
            return $this->json([
                'totalProducts' => count($productValues),
                'products' => $productValues
            ]);
        } catch (\Exception $e) {
            return $this->json(['error' => 'Error al obtener valor de inventario: ' . $e->getMessage()], 500);
        }
    }
}
