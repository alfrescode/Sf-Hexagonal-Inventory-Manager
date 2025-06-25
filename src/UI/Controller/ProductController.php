<?php

namespace App\UI\Controller;

use App\UI\Rest\DTO\CreateProductRequest;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Application\Query\GetProduct\GetProductQuery;
use App\Application\Query\GetProduct\GetProductHandler;
use App\Application\Query\ListProducts\ListProductsQuery;
use App\Domain\Product\Exception\ProductNotFoundException;
use App\Application\Query\ListProducts\ListProductsHandler;
use App\Application\Command\CreateProduct\CreateProductCommand;
use App\Application\Command\CreateProduct\CreateProductHandler;
use App\Application\Command\DeleteProduct\DeleteProductCommand;
use App\Application\Command\DeleteProduct\DeleteProductHandler;
use App\Application\Command\UpdateProduct\UpdateProductCommand;
use App\Application\Command\UpdateProduct\UpdateProductHandler;
use App\Domain\Product\Contract\ProductMongoRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProductController extends AbstractController
{
    public function __construct(
        private ProductMongoRepositoryInterface $mongoRepository
    ) {}

    #[Route('/api/products-mongo', methods: ['POST'])]
    public function createInMongo(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        
        // Implementar la lógica de creación usando el repositorio MongoDB
        // Similar al endpoint existente pero usando $this->mongoRepository
        
        return new JsonResponse(['status' => 'created'], 201);
    }
    // Define la ruta para crear productos vía POST en /api/products
    #[Route('/api/products-mongo', name: 'api_create_product', methods: ['POST'])]
    public function create(
        Request $request, // Inyecta la petición HTTP
        CreateProductHandler $handler // Inyecta el handler del comando
    ): JsonResponse // Devuelve una respuesta JSON
    {
        // Decodifica el contenido JSON de la petición a un array asociativo
        $data = json_decode($request->getContent(), true);

        // Crea un DTO a partir del array de datos
        $dto = CreateProductRequest::fromArray($data);

        // Crea el comando con los datos del DTO
        $command = new CreateProductCommand(
            $dto->name,
            $dto->description,
            $dto->price,
            $dto->stock,
            $dto->variants
        );

        // Ejecuta el handler pasando el comando (crea el producto)
        $handler($command);

        // Devuelve una respuesta JSON con estado 'ok' y código HTTP 201 (creado)
        return $this->json(['status' => 'ok'], 201);
    }

    // Define la ruta para eliminar un producto vía DELETE en /api/products/{id}
    #[Route('/api/products/{id}', name: 'api_delete_product', methods: ['DELETE'])]
    public function delete(
        string $id,
        DeleteProductHandler $handler
    ): JsonResponse
    {
        try {
            // Crear el comando con el ID del producto
            $command = new DeleteProductCommand($id);
            
            // Ejecutar el handler para eliminar el producto
            $handler($command);
            
            // Devolver una respuesta exitosa (204 No Content)
            return new JsonResponse(null, 204);
        } catch (ProductNotFoundException $exception) {
            // Si el producto no existe, devolver un error 404
            return $this->json(['error' => $exception->getMessage()], 404);
        } catch (\Exception $exception) {
            // Para cualquier otro error, devolver un error 500
            return $this->json(['error' => 'Error interno del servidor'], 500);
        }
    }

    // Define la ruta para actualizar un producto vía PUT en /api/products/{id}
    #[Route('/api/products/{id}', name: 'api_update_product', methods: ['PUT'])]
    public function update(
        string $id,
        Request $request,
        UpdateProductHandler $handler
    ): JsonResponse
    {
        try {
            // Decodifica el contenido JSON de la petición a un array asociativo
            $data = json_decode($request->getContent(), true);
            
            // Crea el comando con los datos de la petición
            $command = new UpdateProductCommand(
                $id,
                $data['name'] ?? null,
                $data['description'] ?? null,
                isset($data['price']) ? (float)$data['price'] : null,
                isset($data['stock']) ? (int)$data['stock'] : null,
                $data['variants'] ?? null
            );
            
            // Ejecuta el handler para actualizar el producto
            $handler($command);
            
            // Devuelve una respuesta JSON con estado 'ok'
            return $this->json(['status' => 'ok']);
        } catch (ProductNotFoundException $exception) {
            // Si el producto no existe, devolver un error 404
            return $this->json(['error' => $exception->getMessage()], 404);
        } catch (\Exception $exception) {
            // Para cualquier otro error, devolver un error 500
            return $this->json(['error' => 'Error interno del servidor: ' . $exception->getMessage()], 500);
        }
    }

    // Define la ruta para obtener todos los productos vía GET en /api/products
    #[Route('/api/products', name: 'api_list_products', methods: ['GET'])]
    public function list(
        Request $request,
        ListProductsHandler $handler
    ): JsonResponse
    {
        try {
            // Obtener parámetros de paginación y filtrado de la petición
            $page = $request->query->getInt('page', 1);
            $limit = $request->query->getInt('limit', 10);
            $filters = $request->query->all('filters');
            
            // Crear la consulta
            $query = new ListProductsQuery($page, $limit, $filters);
            
            // Ejecutar el handler para obtener la lista de productos
            $productsListDTO = $handler($query);
            
            // Devolver la respuesta JSON con los productos
            return $this->json($productsListDTO);
        } catch (\Exception $exception) {
            // Para cualquier error, devolver un error 500
            return $this->json(['error' => 'Error interno del servidor: ' . $exception->getMessage()], 500);
        }
    }

    // Define la ruta para obtener un producto específico vía GET en /api/products/{id}
    #[Route('/api/products/{id}', name: 'api_get_product', methods: ['GET'])]
    public function get(
        string $id,
        GetProductHandler $handler
    ): JsonResponse
    {
        try {
            // Crear la consulta con el ID del producto
            $query = new GetProductQuery($id);
            
            // Ejecutar el handler para obtener el producto
            $productDTO = $handler($query);
            
            // Devolver la respuesta JSON con el producto
            return $this->json($productDTO);
        } catch (ProductNotFoundException $exception) {
            // Si el producto no existe, devolver un error 404
            return $this->json(['error' => $exception->getMessage()], 404);
        } catch (\Exception $exception) {
            // Para cualquier otro error, devolver un error 500
            return $this->json(['error' => 'Error interno del servidor: ' . $exception->getMessage()], 500);
        }
    }
}
