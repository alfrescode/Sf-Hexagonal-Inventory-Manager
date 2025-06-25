<?php

namespace App\UI\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use App\Application\Service\InventoryService;
use App\Domain\Product\Exception\ProductNotFoundException;
use App\Domain\Product\Exception\InsufficientStockException;

/**
 * Controlador para operaciones relacionadas con el inventario
 */
#[Route('/api/inventory')]
class InventoryController extends AbstractController
{
    /**
     * Ajusta el stock de un producto
     */
    #[Route('/adjust/{id}', name: 'api_inventory_adjust', methods: ['POST'])]
    public function adjustStock(
        string $id,
        Request $request,
        InventoryService $inventoryService
    ): JsonResponse {
        try {
            $data = json_decode($request->getContent(), true);
            
            if (!isset($data['quantity']) || !is_numeric($data['quantity'])) {
                return $this->json(['error' => 'Se requiere una cantidad válida'], 400);
            }
            
            $quantity = (int) $data['quantity'];
            $reason = $data['reason'] ?? 'Ajuste manual';
            
            $inventoryService->adjustStock($id, $quantity, $reason);
            
            return $this->json([
                'status' => 'ok',
                'message' => 'Stock ajustado correctamente'
            ]);
        } catch (ProductNotFoundException $e) {
            return $this->json(['error' => $e->getMessage()], 404);
        } catch (InsufficientStockException $e) {
            return $this->json(['error' => $e->getMessage()], 400);
        } catch (\Exception $e) {
            return $this->json(['error' => 'Error interno del servidor: ' . $e->getMessage()], 500);
        }
    }
    
    /**
     * Busca productos según criterios específicos
     */
    #[Route('/search', name: 'api_inventory_search', methods: ['GET'])]
    public function searchProducts(
        Request $request,
        InventoryService $inventoryService
    ): JsonResponse {
        try {
            $criteria = [];
            
            // Recoger criterios de búsqueda
            if ($request->query->has('name')) {
                $criteria['name'] = $request->query->get('name');
            }
            
            if ($request->query->has('minPrice')) {
                $criteria['minPrice'] = (float) $request->query->get('minPrice');
            }
            
            if ($request->query->has('maxPrice')) {
                $criteria['maxPrice'] = (float) $request->query->get('maxPrice');
            }
            
            if ($request->query->has('minStock')) {
                $criteria['minStock'] = (int) $request->query->get('minStock');
            }
            
            // Buscar productos
            $products = $inventoryService->searchProducts($criteria);
            
            // Transformar a array para la respuesta JSON
            $productsData = array_map(function ($product) {
                return [
                    'id' => $product->getId()->value(),
                    'name' => $product->getName()->value(),
                    'description' => $product->getDescription(),
                    'price' => $product->getPrice()->value(),
                    'stock' => $product->getStock()->value(),
                    'variants' => count($product->getVariants())
                ];
            }, $products);
            
            return $this->json([
                'total' => count($productsData),
                'products' => $productsData
            ]);
        } catch (\Exception $e) {
            return $this->json(['error' => 'Error interno del servidor: ' . $e->getMessage()], 500);
        }
    }
    
    /**
     * Obtiene productos con stock bajo
     */
    #[Route('/low-stock', name: 'api_inventory_low_stock', methods: ['GET'])]
    public function getLowStockProducts(
        Request $request,
        InventoryService $inventoryService
    ): JsonResponse {
        try {
            $threshold = $request->query->getInt('threshold', 5);
            
            $criteria = ['maxStock' => $threshold];
            $products = $inventoryService->searchProducts($criteria);
            
            // Transformar a array para la respuesta JSON
            $productsData = array_map(function ($product) {
                return [
                    'id' => $product->getId()->value(),
                    'name' => $product->getName()->value(),
                    'stock' => $product->getStock()->value(),
                    'price' => $product->getPrice()->value()
                ];
            }, $products);
            
            return $this->json([
                'total' => count($productsData),
                'threshold' => $threshold,
                'products' => $productsData
            ]);
        } catch (\Exception $e) {
            return $this->json(['error' => 'Error interno del servidor: ' . $e->getMessage()], 500);
        }
    }
}
