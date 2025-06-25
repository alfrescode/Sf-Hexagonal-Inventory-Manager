<?php

namespace App\UI\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Application\Query\GetProduct\GetProductQuery;
use App\Application\Query\GetProduct\GetProductHandler;
use App\Application\Query\ListProducts\ListProductsQuery;
use App\Domain\Product\Exception\ProductNotFoundException;
use App\Application\Query\ListProducts\ListProductsHandler;
use App\Application\Command\CreateProduct\CreateProductCommand;
use App\Application\Command\CreateProduct\CreateProductHandler;
use App\UI\Form\ProductType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Controlador para gestionar la interfaz web de productos
 */
#[Route('/productos')]
class ProductWebController extends AbstractController
{
    /*
        * Creacion de productos
        * Este controlador maneja las rutas y acciones relacionadas con la creación de productos.
        */
    #[Route('/crear', name: 'app_product_create')]
    public function create(Request $request, CreateProductHandler $handler): Response
    {
        $form = $this->createForm(ProductType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $command = new CreateProductCommand(
                $data['name'],
                $data['description'],
                $data['price'],
                $data['stock'],
                [] // Variantes vacías por ahora
            );
            $handler($command);
            $this->addFlash('success', 'Producto creado correctamente');
            return $this->redirectToRoute('app_product_list');
        }

        return $this->render('product/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * Muestra la lista de productos
     */
    #[Route('/', name: 'app_product_list')]
    public function list(Request $request, ListProductsHandler $handler): Response
    {
        $page = $request->query->getInt('page', 1);
        $limit = $request->query->getInt('limit', 10);
        
        $query = new ListProductsQuery($page, $limit);
        $productsListDTO = $handler($query);
        
        return $this->render('product/list.html.twig', [
            'productsList' => $productsListDTO
        ]);
    }
    
    /**
     * Muestra el detalle de un producto
     */
    #[Route('/{id}', name: 'app_product_show')]
    public function show(string $id, GetProductHandler $handler): Response
    {
        try {
            $query = new GetProductQuery($id);
            $productDTO = $handler($query);
            
            return $this->render('product/show.html.twig', [
                'product' => $productDTO
            ]);
        } catch (ProductNotFoundException $e) {
            $this->addFlash('error', $e->getMessage());
            return $this->redirectToRoute('app_product_list');
        }
    }
}
