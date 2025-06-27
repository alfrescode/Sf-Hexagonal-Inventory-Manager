<?php
namespace App\Command;

use App\Application\Service\ProductService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateProductCommand extends Command
{
    protected static $defaultName = 'app:create-product';

    private ProductService $productService;

    public function __construct(ProductService $productService)
    {
        parent::__construct();
        $this->productService = $productService;
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Crea un nuevo producto.')
            ->addArgument('name', InputArgument::REQUIRED, 'Nombre del producto')
            ->addArgument('description', InputArgument::REQUIRED, 'Descripción del producto')
            ->addArgument('price', InputArgument::REQUIRED, 'Precio del producto')
            ->addArgument('stock', InputArgument::REQUIRED, 'Cantidad en stock');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $name = $input->getArgument('name');
        $description = $input->getArgument('description');
        $price = (float) $input->getArgument('price');
        $stock = (int) $input->getArgument('stock');

        try {
            $product = $this->productService->createProduct($name, $description, $price, $stock);

            $output->writeln('<info>Producto creado exitosamente:</info>');
            $output->writeln(sprintf('ID: %s', $product->getId()->getValue()));
            $output->writeln(sprintf('Nombre: %s', $product->getName()->getValue()));
            $output->writeln(sprintf('Descripción: %s', $product->getDescription()->getValue()));
            $output->writeln(sprintf('Precio: %.2f', $product->getPrice()->getValue()));
            $output->writeln(sprintf('Stock: %d', $product->getStock()->getValue()));

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $output->writeln('<error>Error al crear el producto:</error> ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}