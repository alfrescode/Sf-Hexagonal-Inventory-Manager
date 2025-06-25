# Guía Detallada: Sistema de Gestión de Inventario con Symfony y Arquitectura Hexagonal

Este documento proporciona una explicación exhaustiva del sistema de gestión de inventario implementado con Symfony y siguiendo los principios de Domain-Driven Design (DDD) y Arquitectura Hexagonal (también conocida como Puertos y Adaptadores).

## Índice

1. [Introducción para desarrolladores de Laravel](#introducción-para-desarrolladores-de-laravel)
2. [Estructura del Proyecto](#estructura-del-proyecto)
3. [Conceptos Clave](#conceptos-clave)
4. [Diagrama de Flujo](#diagrama-de-flujo)
5. [Explicación de Componentes](#explicación-de-componentes)
6. [Comparativa con Laravel](#comparativa-con-laravel)
7. [Ejemplos de Uso](#ejemplos-de-uso)
8. [Extensión del Sistema](#extensión-del-sistema)

## Introducción para desarrolladores de Laravel

Si vienes de Laravel, este proyecto puede parecer estructurado de manera muy diferente a lo que estás acostumbrado. Mientras que Laravel sigue una arquitectura MVC (Modelo-Vista-Controlador) bastante tradicional, este proyecto utiliza una **Arquitectura Hexagonal** junto con **Domain-Driven Design (DDD)**, lo cual separa la lógica en capas más definidas y aisladas.

### Diferencias clave que notarás:

1. **No hay modelos en el sentido tradicional de Laravel**. En lugar de modelos Eloquent, tenemos "Entidades de Dominio" que representan nuestros objetos de negocio.

2. **No hay un ORM directamente en el dominio**. Las entidades del dominio no conocen nada sobre la persistencia (a diferencia de los modelos Eloquent que tienen métodos como `save()`, `find()`, etc.).

3. **El flujo de datos es diferente**. En Laravel típicamente tienes:
   ```
   Route → Controller → Model → View
   ```

   En este proyecto, el flujo es:
   ```
   Controller → Command/Query → Handler → Domain → Repository → Infrastructure (DB)
   ```

4. **Separación más estricta**. En Laravel puedes fácilmente acceder a la base de datos desde cualquier parte. Aquí, el dominio está completamente aislado de la infraestructura.

## Estructura del Proyecto

La estructura sigue los principios de la Arquitectura Hexagonal, organizando el código en tres capas principales:

### 1. Dominio (src/Domain)
El núcleo de la aplicación donde reside la lógica de negocio.
```
src/
  Domain/
    Product/
      Contract/       # Interfaces (Puertos)
      Event/          # Eventos de dominio
      Exception/      # Excepciones específicas del dominio
      ValueObject/    # Objetos de valor inmutables
      Product.php     # Entidad principal
      ProductVariant.php  # Entidad relacionada
```

### 2. Aplicación (src/Application)
Coordina el flujo de la lógica de negocio pero no contiene reglas de negocio en sí.
```
src/
  Application/
    Command/        # Comandos para modificar el estado (similar a FormRequests en Laravel)
      CreateProduct/
      UpdateProduct/
      DeleteProduct/
    Query/          # Consultas para obtener datos (sin modificar estado)
      GetProduct/
      ListProducts/
    Event/          # Manejadores de eventos
```

### 3. Infraestructura (src/Infrastructure)
Implementaciones concretas de las interfaces definidas en el dominio.
```
src/
  Infrastructure/
    Persistence/
      Doctrine/
        Repository/  # Implementaciones concretas de los repositorios
    Controller/      # Controladores que reciben peticiones HTTP
```

### 4. Interfaz de Usuario (src/UI)
Puntos de entrada a la aplicación desde diferentes interfaces.
```
src/
  UI/
    CLI/            # Comandos de consola
    Rest/           # API REST
      Controller/   # Controladores específicos para la API
```

## Conceptos Clave

### 1. Entidades y Objetos de Valor
- **Entidades**: Objetos con identidad única (ej: `Product`)
- **Value Objects**: Objetos inmutables sin identidad (ej: `ProductId`, `ProductName`)

### 2. Patrón CQRS (Command Query Responsibility Segregation)
Separa las operaciones que modifican estado (Commands) de las que consultan datos (Queries):

- **Commands**: `CreateProductCommand`, `UpdateProductCommand`, `DeleteProductCommand`
- **Queries**: `GetProductQuery`, `ListProductsQuery`

### 3. Event-Driven Architecture
El sistema utiliza eventos para comunicar cambios entre componentes:

- **Domain Events**: `ProductCreatedEvent`, `ProductUpdatedEvent`, `ProductDeletedEvent`
- **Event Listeners**: `ProductCreatedListener`, `ProductUpdatedListener`, `ProductDeletedListener`

## Diagrama de Flujo

```
┌─────────────┐     ┌───────────────┐     ┌───────────────┐     ┌─────────────────┐
│             │     │               │     │               │     │                 │
│  UI Layer   │────▶│ Command/Query │────▶│    Handler    │────▶│ Domain Service/ │
│ (Controller)│     │    Object     │     │               │     │   Repository    │
│             │     │               │     │               │     │                 │
└─────────────┘     └───────────────┘     └───────┬───────┘     └────────┬────────┘
                                                  │                      │
                                                  │                      │
                                                  ▼                      ▼
                                         ┌─────────────────┐    ┌─────────────────┐
                                         │                 │    │                 │
                                         │  Domain Model   │    │ Infrastructure  │
                                         │   (Entities)    │    │    (Database)   │
                                         │                 │    │                 │
                                         └────────┬────────┘    └─────────────────┘
                                                  │
                                                  │
                                                  ▼
                                         ┌─────────────────┐
                                         │                 │
                                         │  Domain Events  │
                                         │                 │
                                         └────────┬────────┘
                                                  │
                                                  │
                                                  ▼
                                         ┌─────────────────┐
                                         │                 │
                                         │ Event Listeners │
                                         │                 │
                                         └─────────────────┘
```

## Explicación de Componentes

### 1. Entidad Product

La clase `Product` es la entidad principal que representa un producto en el sistema:

```php
final class Product
{
    private ProductId $id;
    private ProductName $name;
    private string $description;
    private ProductPrice $price;
    private ProductStock $stock;
    private array $variants;

    // Constructor, getters y setters
}
```

A diferencia de los modelos en Laravel:
- No extiende de ninguna clase base como `Model`
- No tiene métodos como `save()`, `find()`, etc.
- Los atributos son objetos de valor tipados, no simplemente propiedades

### 2. Comandos y Handlers

#### CreateProductCommand
```php
class CreateProductCommand
{
    public string $name;
    public string $description;
    public float $price;
    public int $stock;
    public array $variants;

    // Constructor
}
```

#### CreateProductHandler
```php
class CreateProductHandler
{
    private ProductRepositoryInterface $repository;
    private ?EventDispatcherInterface $eventDispatcher;

    // Constructor

    public function __invoke(CreateProductCommand $command): void
    {
        // 1. Crear objeto Product
        // 2. Guardarlo usando el repositorio
        // 3. Despachar evento ProductCreatedEvent
    }
}
```

Esto es similar a los Form Requests + Controllers en Laravel, pero con una separación más clara de responsabilidades.

### 3. Repositorios

La interfaz del repositorio define los métodos para acceder a los productos:

```php
interface ProductRepositoryInterface
{
    public function find(ProductId $id): ?Product;
    public function findAll(int $page = 1, int $limit = 10): array;
    public function save(Product $product): void;
    public function delete(ProductId $id): void;
}
```

La implementación concreta se realiza en la capa de infraestructura:

```php
class DoctrineProductRepository implements ProductRepositoryInterface
{
    // Implementación usando Doctrine ORM
}
```

Esto es similar a los Repositories en Laravel, pero aquí son obligatorios y están definidos por interfaces.

### 4. Eventos de Dominio

Los eventos de dominio permiten desacoplar acciones que ocurren cuando cambia el estado del sistema:

```php
class ProductCreatedEvent
{
    private Product $product;

    // Constructor y getters
}
```

Los listeners procesan estos eventos:

```php
class ProductCreatedListener implements EventSubscriberInterface
{
    private LoggerInterface $logger;

    // Constructor

    public function onProductCreated(ProductCreatedEvent $event): void
    {
        // Registrar la creación, enviar emails, etc.
    }
}
```

Esto es similar a los eventos en Laravel, pero más orientados al dominio y no a aspectos técnicos.

## Comparativa con Laravel

| Aspecto | Laravel | Este Proyecto (Symfony + Hexagonal) |
|---------|---------|-------------------------------------|
| **Modelos** | Modelos Eloquent con ORM integrado | Entidades de dominio puras + Repositorios separados |
| **Controladores** | Controllers que manejan requests y respuestas | Controllers ligeros que delegan a Commands/Queries |
| **Rutas** | Routes que mapean URLs a Controllers | Igual, pero con más capas entre medio |
| **Validación** | Form Requests o validación en controllers | Validación dentro de Handlers o Services |
| **Eventos** | Events y Listeners más orientados a infraestructura | Events y Listeners de dominio |
| **Dependencias** | Service Container | Service Container (pero con interfaces explícitas) |

## Ejemplos de Uso

### 1. Crear un Producto

```php
// En un controlador:
public function create(Request $request, CommandBusInterface $commandBus): Response
{
    $command = new CreateProductCommand(
        $request->get('name'),
        $request->get('description'),
        (float)$request->get('price'),
        (int)$request->get('stock'),
        $request->get('variants', [])
    );
    
    $commandBus->dispatch($command);
    
    return new JsonResponse(['status' => 'Product created'], Response::HTTP_CREATED);
}
```

### 2. Obtener un Producto

```php
// En un controlador:
public function get(string $id, QueryBusInterface $queryBus): Response
{
    $query = new GetProductQuery($id);
    
    try {
        $productDTO = $queryBus->dispatch($query);
        return new JsonResponse($productDTO);
    } catch (ProductNotFoundException $e) {
        return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_NOT_FOUND);
    }
}
```

## Extensión del Sistema

Para añadir una nueva funcionalidad (por ejemplo, categorías de productos):

1. **Dominio**: Crear la entidad `Category` y sus value objects
2. **Repositorio**: Definir una interfaz `CategoryRepositoryInterface` 
3. **Commands/Queries**: Crear comandos y consultas para gestionar categorías
4. **Handlers**: Implementar la lógica de negocio en los handlers
5. **Infraestructura**: Crear la implementación del repositorio con Doctrine
6. **UI**: Añadir endpoints en la API o comandos CLI

La ventaja de esta arquitectura es que puedes cambiar cualquier capa sin afectar a las demás, siempre que respetes las interfaces.

---

Este documento ofrece una visión general del sistema. Para más detalles sobre cada componente, puedes consultar los comentarios en el código fuente y los tests unitarios que muestran cómo se utilizan las diferentes partes del sistema.
