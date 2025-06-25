<?php
// Define el espacio de nombres donde se encuentra la clase
namespace App\UI\Rest\DTO;

// Define la clase CreateProductRequest
class CreateProductRequest
{
    // Propiedad pública para el nombre del producto
    public string $name;
    // Propiedad pública para la descripción del producto
    public string $description;
    // Propiedad pública para el precio del producto
    public float $price;
    // Propiedad pública para el stock del producto
    public int $stock;
    /** 
     * Propiedad pública para las variantes del producto.
     * Es un array de arrays, donde la clave es un entero.
     */
    public array $variants = [];

    // Método estático que crea una instancia de la clase a partir de un array
    public static function fromArray(array $data): self
    {
        // Crea una nueva instancia de la clase
        $self = new self();
        // Asigna el nombre desde el array, o una cadena vacía si no existe
        $self->name = $data['name'] ?? '';
        // Asigna la descripción desde el array, o una cadena vacía si no existe
        $self->description = $data['description'] ?? '';
        // Asigna el precio desde el array, convirtiéndolo a float, o 0 si no existe
        $self->price = (float)($data['price'] ?? 0);
        // Asigna el stock desde el array, convirtiéndolo a int, o 0 si no existe
        $self->stock = (int)($data['stock'] ?? 0);
        // Asigna las variantes desde el array, o un array vacío si no existe
        $self->variants = $data['variants'] ?? [];
        // Devuelve la instancia creada y rellenada
        return $self;
    }
}