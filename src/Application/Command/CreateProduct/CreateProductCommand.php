<?php
namespace App\Application\Command\CreateProduct; // Define el espacio de nombres para organizar el código y evitar conflictos de nombres.

class CreateProductCommand // Declara la clase CreateProductCommand.
{
    public string $name;
    public string $description;
    public float $price;
    public int $stock;
    public array $variants;

    public function __construct(string $name, string $description, float $price, int $stock, array $variants = [])
    {
        $this->name = $name; // Asigna el nombre recibido al atributo $name.
        $this->description = $description; // Asigna la descripción recibida al atributo $description.
        $this->price = $price; // Asigna el precio recibido al atributo $price.
        $this->stock = $stock; // Asigna el stock recibido al atributo $stock.
        $this->variants = $variants; // Asigna las variantes recibidas al atributo $variants. Cada variante es un array asociativo con detalles como talla, color, precio, stock e imagen.
    }
}