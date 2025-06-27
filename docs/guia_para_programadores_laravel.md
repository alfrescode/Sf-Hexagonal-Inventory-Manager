# Guía Exhaustiva: Sistema de Gestión de Inventario con Symfony, DDD y Arquitectura Hexagonal

## Introducción

Este documento proporciona una explicación detallada del Sistema de Gestión de Inventario implementado con Symfony, Domain-Driven Design (DDD) y Arquitectura Hexagonal. Está diseñado específicamente para programadores familiarizados con Laravel que deseen entender cómo funcionan estos paradigmas en el contexto de Symfony.

## Índice

1. [Fundamentos Teóricos](#fundamentos-teóricos)
   - [Domain-Driven Design (DDD)](#domain-driven-design-ddd)
   - [Arquitectura Hexagonal](#arquitectura-hexagonal)
   - [CQRS (Command Query Responsibility Segregation)](#cqrs-command-query-responsibility-segregation)
   - [Event-Driven Architecture](#event-driven-architecture)

2. [Estructura del Proyecto](#estructura-del-proyecto)
   - [Visión General](#visión-general)
   - [Capa de Dominio](#capa-de-dominio)
   - [Capa de Aplicación](#capa-de-aplicación)
   - [Capa de Infraestructura](#capa-de-infraestructura)
   - [Capa de UI](#capa-de-ui)

3. [Flujo de Trabajo Detallado](#flujo-de-trabajo-detallado)
   - [Proceso de Creación de un Producto](#proceso-de-creación-de-un-producto)
   - [Proceso de Consulta de un Producto](#proceso-de-consulta-de-un-producto)
   - [Proceso de Actualización de un Producto](#proceso-de-actualización-de-un-producto)
   - [Proceso de Eliminación de un Producto](#proceso-de-eliminación-de-un-producto)

4. [Comparación con Laravel](#comparación-con-laravel)
   - [Diferencias Arquitectónicas](#diferencias-arquitectónicas)
   - [Manejo de Bases de Datos](#manejo-de-bases-de-datos)
   - [Manejo de Eventos](#manejo-de-eventos)
   - [Routing y Controladores](#routing-y-controladores)

5. [Componentes Clave Explicados](#componentes-clave-explicados)
   - [Value Objects](#value-objects)
   - [Entidades y Agregados](#entidades-y-agregados)
   - [Repositorios](#repositorios)
   - [Comandos y Handlers](#comandos-y-handlers)
   - [Queries y Handlers](#queries-y-handlers)
   - [Eventos de Dominio](#eventos-de-dominio)

6. [Patrones Implementados](#patrones-implementados)
   - [Factory](#factory)
   - [Repository](#repository)
   - [Command Bus](#command-bus)
   - [Query Bus](#query-bus)
   - [Event Dispatcher](#event-dispatcher)

7. [Ejemplos Prácticos](#ejemplos-prácticos)
   - [Añadir un Nuevo Value Object](#añadir-un-nuevo-value-object)
   - [Crear un Nuevo Comando](#crear-un-nuevo-comando)
   - [Implementar un Nuevo Endpoint REST](#implementar-un-nuevo-endpoint-rest)
   - [Manejar un Nuevo Evento de Dominio](#manejar-un-nuevo-evento-de-dominio)

8. [Testing](#testing)
   - [Testing por Capas](#testing-por-capas)
   - [Ejemplos de Tests](#ejemplos-de-tests)

9. [Consideraciones Avanzadas](#consideraciones-avanzadas)
   - [Rendimiento](#rendimiento)
   - [Escalabilidad](#escalabilidad)
   - [Mantenibilidad](#mantenibilidad)

10. [Glosario](#glosario)

## Fundamentos Teóricos

### Domain-Driven Design (DDD)

El Domain-Driven Design es un enfoque para el desarrollo de software que se centra en el modelado del dominio, la parte central de la aplicación que contiene la lógica de negocio. DDD propone:

- **Lenguaje Ubicuo**: Un lenguaje común compartido entre desarrolladores y expertos del dominio.
- **Modelo Rico**: Entidades con comportamiento, no solo datos.
- **Capas Delimitadas**: Separación clara entre el dominio y el resto de la aplicación.
- **Agregados**: Grupos de entidades que se tratan como una unidad.
- **Value Objects**: Objetos inmutables definidos por sus atributos.
- **Servicios de Dominio**: Operaciones que no pertenecen naturalmente a entidades o value objects.
- **Repositorios**: Abstracción para el acceso a datos de entidades.
- **Eventos de Dominio**: Notificaciones de cambios significativos en el dominio.

En nuestro sistema, estos conceptos se materializan de la siguiente manera:

- **Agregado**: `Product` como entidad raíz que contiene lógica de negocio.
- **Value Objects**: `ProductId`, `ProductName`, `ProductPrice`, `ProductStock`.
- **Eventos**: `ProductCreatedEvent`, `ProductUpdatedEvent`, `ProductDeletedEvent`.
- **Repositorio**: Interface `ProductRepositoryInterface` en el dominio.

### Arquitectura Hexagonal

También conocida como "Ports and Adapters", la Arquitectura Hexagonal propone:

- **Núcleo de Aplicación**: Contiene la lógica de negocio y es independiente de factores externos.
- **Puertos**: Interfaces que define el núcleo para comunicarse con el exterior.
- **Adaptadores**: Implementaciones específicas de los puertos para distintas tecnologías.

En nuestro sistema:

- **Puertos**: Interfaces como `ProductRepositoryInterface`.
- **Adaptadores**: Implementaciones como `DoctrineProductRepository`.

La ventaja principal es que podemos cambiar la tecnología de persistencia, UI o servicios externos sin modificar el núcleo de la aplicación.

### CQRS (Command Query Responsibility Segregation)

CQRS separa las operaciones que modifican el estado (Comandos) de las operaciones que retornan datos (Queries).

- **Comandos**: Representan intenciones de modificar el estado del sistema.
- **Queries**: Representan intenciones de obtener información del sistema.

En nuestro sistema:

- **Comandos**: `CreateProductCommand`, `UpdateProductCommand`, `DeleteProductCommand`.
- **Queries**: `GetProductQuery`, `ListProductsQuery`.

### Event-Driven Architecture

Una arquitectura basada en eventos donde los componentes del sistema se comunican mediante eventos:

- **Eventos**: Notificaciones de algo que ha ocurrido.
- **Listeners**: Reaccionan a eventos específicos.

En nuestro sistema:

- **Eventos**: `ProductCreatedEvent`, `ProductUpdatedEvent`, `ProductDeletedEvent`.
- **Listeners**: `ProductCreatedListener`, `ProductUpdatedListener`, `ProductDeletedListener`.

## Estructura del Proyecto

### Visión General

La estructura del proyecto sigue una organización por capas, alineada con los principios de DDD y Arquitectura Hexagonal:

```
src/
├── Domain/            # Núcleo, contiene la lógica de negocio
├── Application/       # Casos de uso de la aplicación
├── Infrastructure/    # Implementaciones técnicas
└── UI/                # Interfaces de usuario (REST, CLI)
```

### Capa de Dominio

La capa de dominio contiene las entidades, value objects, eventos y interfaces que representan el modelo de negocio.

```
Domain/
├── Product/                  # Agregado Product
│   ├── Contract/             # Interfaces/Puertos
│   │   └── ProductRepositoryInterface.php
│   ├── Event/                # Eventos de dominio
│   │   ├── ProductCreatedEvent.php
│   │   ├── ProductUpdatedEvent.php
│   │   └── ProductDeletedEvent.php
│   ├── ValueObject/          # Value Objects
│   │   ├── ProductId.php
│   │   ├── ProductName.php
│   │   ├── ProductPrice.php
│   │   └── ProductStock.php
│   ├── Product.php           # Entidad raíz
│   └── ProductVariant.php    # Entidad
└── Shared/                   # Componentes compartidos
    └── ValueObject/
        ├── Uuid.php
        └── Money.php
```

#### Value Objects

Los Value Objects son objetos inmutables definidos por sus atributos. Por ejemplo, `ProductId`:

```php
final class ProductId
{
    private string $value;

    public function __construct(string $value)
    {
        $this->value = $value;
    }

    public function value(): string
    {
        return $this->value;
    }

    public function equals(ProductId $other): bool
    {
        return $this->value === $other->value();
    }
}
```

#### Entidades

Las entidades son objetos con identidad. Por ejemplo, `Product`:

```php
final class Product
{
    private ProductId $id;
    private ProductName $name;
    private string $description;
    private ProductPrice $price;
    private ProductStock $stock;
    private array $variants;

    public function __construct(
        ProductId $id,
        ProductName $name,
        string $description,
        ProductPrice $price,
        ProductStock $stock,
        array $variants = []
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
        $this->price = $price;
        $this->stock = $stock;
        $this->variants = $variants;
    }

    // Getters y métodos de dominio
}
```

### Capa de Aplicación

La capa de aplicación contiene los casos de uso del sistema, implementados mediante el patrón CQRS:

```
Application/
├── Command/                  # Comandos (modifican estado)
│   ├── CreateProduct/
│   │   ├── CreateProductCommand.php
│   │   └── CreateProductHandler.php
│   ├── UpdateProduct/
│   │   ├── UpdateProductCommand.php
│   │   └── UpdateProductHandler.php
│   └── DeleteProduct/
│       ├── DeleteProductCommand.php
│       └── DeleteProductHandler.php
├── Query/                    # Consultas (leen estado)
│   ├── GetProduct/
│   │   ├── GetProductQuery.php
│   │   └── GetProductHandler.php
│   └── ListProducts/
│       ├── ListProductsQuery.php
│       └── ListProductsHandler.php
└── Event/                    # Listeners de eventos
    ├── ProductCreatedListener.php
    ├── ProductUpdatedListener.php
    └── ProductDeletedListener.php
```

#### Comandos

Los comandos representan intenciones de modificar el estado. Por ejemplo, `CreateProductCommand`:

```php
class CreateProductCommand
{
    public string $name;
    public string $description;
    public float $price;
    public int $stock;
    public array $variants;

    public function __construct(string $name, string $description, float $price, int $stock, array $variants = [])
    {
        $this->name = $name;
        $this->description = $description;
        $this->price = $price;
        $this->stock = $stock;
        $this->variants = $variants;
    }
}
```

#### Handlers

Los handlers ejecutan la lógica asociada a un comando o query. Por ejemplo, `CreateProductHandler`:

```php
class CreateProductHandler
{
    private ProductRepositoryInterface $repository;
    private ?EventDispatcherInterface $eventDispatcher;

    public function __construct(
        ProductRepositoryInterface $repository,
        ?EventDispatcherInterface $eventDispatcher = null
    ) {
        $this->repository = $repository;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function __invoke(CreateProductCommand $command): void
    {
        $product = new Product(
            new ProductId(Uuid::v4()->toRfc4122()),
            new ProductName($command->name),
            $command->description,
            new ProductPrice($command->price),
            new ProductStock($command->stock),
            array_map(function ($variant) {
                return new ProductVariant(
                    $variant['size'] ?? '',
                    $variant['color'] ?? '',
                    new ProductPrice($variant['price']),
                    new ProductStock($variant['stock']),
                    $variant['imageUrl'] ?? ''
                );
            }, $command->variants)
        );

        $this->repository->save($product);

        if ($this->eventDispatcher) {
            $this->eventDispatcher->dispatch(new ProductCreatedEvent($product));
        }
    }
}
```

### Capa de Infraestructura

La capa de infraestructura contiene las implementaciones concretas de las interfaces definidas en el dominio:

```
Infrastructure/
├── Persistence/               # Implementaciones de persistencia
│   ├── Doctrine/
│   │   ├── Entity/
│   │   │   └── ProductEntity.php
│   │   ├── Repository/
│   │   │   └── DoctrineProductRepository.php
│   │   └── Mapping/
│   │       └── Product.orm.xml
│   └── File/
│       └── FileProductRepository.php
├── Email/                     # Servicios de Email
│   ├── Contract/
│   │   └── EmailSenderInterface.php
│   ├── SmtpMailer.php
│   └── SesMailer.php
└── Service/                   # Servicios de infraestructura
    └── SymfonyEventDispatcher.php
```

#### Repositorios

Las implementaciones de repositorio convierten entidades del dominio a entidades de persistencia y viceversa. Por ejemplo, `DoctrineProductRepository`:

```php
class DoctrineProductRepository implements ProductRepositoryInterface
{
    public function __construct(private EntityManagerInterface $em) {}

    public function save(Product $product): void
    {
        $entity = ProductEntityMapper::toEntity($product);
        $this->em->persist($entity);
        $this->em->flush();
    }

    public function find(ProductId $id): ?Product
    {
        $entity = $this->em->getRepository(ProductEntity::class)->find($id->value());
        return $entity ? ProductEntityMapper::toDomain($entity) : null;
    }

    public function findAll(): array
    {
        $entities = $this->em->getRepository(ProductEntity::class)->findAll();
        return array_map([ProductEntityMapper::class, 'toDomain'], $entities);
    }
    
    public function delete(ProductId $id): void
    {
        $entity = $this->em->getRepository(ProductEntity::class)->find($id->value());
        if ($entity) {
            $this->em->remove($entity);
            $this->em->flush();
        }
    }
}
```

### Capa de UI

La capa de UI contiene los puntos de entrada al sistema:

```
UI/
├── Rest/                      # API REST
│   ├── Controller/
│   │   └── ProductController.php
│   └── DTO/
│       ├── CreateProductRequest.php
│       └── ProductResponse.php
└── CLI/                       # Interfaz de Línea de Comandos
    └── Command/
        └── CreateProductCommand.php
```

#### Controladores REST

Los controladores REST reciben peticiones HTTP, las convierten a comandos o queries, y devuelven respuestas HTTP:

```php
class ProductController extends AbstractController
{
    #[Route('/api/products', name: 'api_create_product', methods: ['POST'])]
    public function create(
        Request $request,
        CreateProductHandler $handler
    ): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $dto = CreateProductRequest::fromArray($data);
        
        $command = new CreateProductCommand(
            $dto->name,
            $dto->description,
            $dto->price,
            $dto->stock,
            $dto->variants
        );
        
        $handler($command);
        
        return $this->json(['status' => 'ok'], 201);
    }
    
    // Otros métodos para GET, PUT, DELETE...
}
```

## Flujo de Trabajo Detallado

### Proceso de Creación de un Producto

1. **Cliente envía POST a `/api/products`**
   ```json
   {
     "name": "Camiseta",
     "description": "Camiseta de algodón",
     "price": 19.99,
     "stock": 100,
     "variants": [
       {
         "size": "S",
         "color": "Rojo",
         "price": 19.99,
         "stock": 30,
         "imageUrl": "https://example.com/camiseta-roja-s.jpg"
       },
       {
         "size": "M",
         "color": "Azul",
         "price": 19.99,
         "stock": 40,
         "imageUrl": "https://example.com/camiseta-azul-m.jpg"
       }
     ]
   }
   ```

2. **El controlador recibe la petición**
   - Convierte el JSON a un objeto DTO (`CreateProductRequest`)
   - Crea un comando (`CreateProductCommand`)
   - Pasa el comando al handler

3. **El handler procesa el comando**
   - Crea un nuevo producto con un ID único
   - Convierte los datos del comando a objetos de dominio (Value Objects)
   - Crea las variantes del producto
   - Guarda el producto a través del repositorio
   - Dispara un evento de dominio (`ProductCreatedEvent`)

4. **El repositorio persiste el producto**
   - Convierte el producto de dominio a entidad de Doctrine
   - Persiste la entidad en la base de datos

5. **Los listeners reaccionan al evento**
   - `ProductCreatedListener` puede realizar acciones como enviar emails, actualizar índices de búsqueda, etc.

6. **El controlador devuelve una respuesta**
   - Devuelve un código 201 (Created) con un mensaje de éxito

### Proceso de Consulta de un Producto

1. **Cliente envía GET a `/api/products/{id}`**

2. **El controlador recibe la petición**
   - Crea una query (`GetProductQuery`)
   - Pasa la query al handler

3. **El handler procesa la query**
   - Convierte el ID a un objeto `ProductId`
   - Consulta el producto a través del repositorio
   - Si el producto existe, lo devuelve como un DTO
   - Si no existe, lanza una excepción

4. **El repositorio recupera el producto**
   - Consulta la entidad en la base de datos
   - Convierte la entidad a un objeto de dominio

5. **El controlador devuelve una respuesta**
   - Si el producto existe, devuelve un código 200 con el DTO del producto
   - Si no existe, devuelve un código 404 con un mensaje de error

## Comparación con Laravel

### Diferencias Arquitectónicas

| Laravel | Symfony + Hexagonal |
|---------|---------------------|
| **Modelos** | Modelos Eloquent que mezclan lógica de negocio y persistencia | Entidades de dominio puras sin conocimiento de persistencia |
| **Controladores** | Controladores que acceden directamente a modelos Eloquent | Controladores que crean comandos/queries y los pasan a handlers |
| **Persistencia** | Directamente integrada en los modelos vía Eloquent | Abstraída detrás de interfaces de repositorio |
| **Eventos** | Eventos Laravel que a menudo mezclan dominio e infraestructura | Eventos de dominio puros con listeners en la capa de aplicación |
| **Validación** | Validación en los controladores o Request objects | Validación en Value Objects del dominio |

### Manejo de Bases de Datos

| Laravel | Symfony + Hexagonal |
|---------|---------------------|
| **ORM** | Eloquent | Doctrine |
| **Migraciones** | Migraciones PHP con Schema Builder | Migraciones gestionadas por Doctrine |
| **Acceso a Datos** | `Model::find()`, `Model::create()`, etc. | Repositorios que implementan interfaces del dominio |
| **Consultas** | Query Builder de Eloquent | DQL (Doctrine Query Language) o QueryBuilder de Doctrine |

### Manejo de Eventos

| Laravel | Symfony + Hexagonal |
|---------|---------------------|
| **Dispatcher** | Event dispatcher de Laravel | Event dispatcher de Symfony o implementación personalizada |
| **Listeners** | Registro en EventServiceProvider | Configuración vía servicios o tags |
| **Propósito** | A menudo usado para efectos secundarios (emails, logs) | Principalmente para mantener consistencia entre agregados |

### Routing y Controladores

| Laravel | Symfony + Hexagonal |
|---------|---------------------|
| **Definición de Rutas** | Routes/web.php o Routes/api.php | Atributos en controladores o YAML/XML |
| **Middleware** | Middleware de Laravel | Eventos de kernel o subscribers |
| **Respuestas** | Directamente desde controladores | Vía handlers y DTOs |

## Componentes Clave Explicados

### Value Objects

Los Value Objects son objetos inmutables definidos por sus atributos, no por una identidad. Algunos beneficios:

- **Inmutabilidad**: No cambian después de crearse
- **Auto-validación**: Validan sus datos en el constructor
- **Comparación por valor**: Dos objetos con los mismos valores son iguales
- **Expresividad**: Expresan conceptos del dominio mejor que tipos primitivos

Ejemplo: `ProductPrice`

```php
final class ProductPrice
{
    private float $amount;

    public function __construct(float $amount)
    {
        if ($amount < 0) {
            throw new InvalidArgumentException('El precio no puede ser negativo');
        }
        $this->amount = $amount;
    }

    public function amount(): float
    {
        return $this->amount;
    }

    public function equals(ProductPrice $other): bool
    {
        return abs($this->amount - $other->amount()) < 0.001;
    }

    public function increaseByPercentage(float $percentage): ProductPrice
    {
        return new self($this->amount * (1 + $percentage / 100));
    }
}
```

### Entidades y Agregados

Las entidades son objetos con identidad. Los agregados son grupos de entidades que se tratan como una unidad.

En nuestro sistema:
- `Product` es un agregado raíz
- `ProductVariant` es una entidad dentro del agregado

La regla clave es que los objetos externos solo pueden referenciar al agregado raíz, nunca a entidades internas.

### Repositorios

Los repositorios abstraen el acceso a datos:

- Definidos como interfaces en el dominio
- Implementados en la infraestructura
- Trabajan con objetos del dominio, no con DTOs o entidades de persistencia

### Comandos y Handlers

Los comandos representan intenciones de modificar el estado:

- Comandos: Simples DTOs con propiedades públicas
- Handlers: Contienen la lógica para ejecutar los comandos

### Queries y Handlers

Las queries representan intenciones de leer el estado:

- Queries: Simples DTOs con propiedades públicas
- Handlers: Contienen la lógica para ejecutar las queries y retornar DTOs

### Eventos de Dominio

Los eventos de dominio representan hechos importantes ocurridos en el dominio:

- Inmutables
- Contienen información sobre lo ocurrido
- Permiten desacoplar efectos secundarios de la lógica principal

## Patrones Implementados

### Factory

El patrón Factory encapsula la lógica de creación de objetos complejos:

```php
class ProductFactory
{
    public function create(
        string $name,
        string $description,
        float $price,
        int $stock,
        array $variants = []
    ): Product
    {
        return new Product(
            new ProductId(Uuid::v4()->toRfc4122()),
            new ProductName($name),
            $description,
            new ProductPrice($price),
            new ProductStock($stock),
            array_map(function ($variant) {
                return $this->createVariant(
                    $variant['size'] ?? '',
                    $variant['color'] ?? '',
                    $variant['price'],
                    $variant['stock'],
                    $variant['imageUrl'] ?? ''
                );
            }, $variants)
        );
    }

    private function createVariant(
        string $size,
        string $color,
        float $price,
        int $stock,
        string $imageUrl
    ): ProductVariant
    {
        return new ProductVariant(
            $size,
            $color,
            new ProductPrice($price),
            new ProductStock($stock),
            $imageUrl
        );
    }
}
```

### Repository

El patrón Repository abstrae el acceso a datos:

```php
interface ProductRepositoryInterface
{
    public function save(Product $product): void;
    public function find(ProductId $id): ?Product;
    public function findAll(): array;
    public function delete(ProductId $id): void;
}
```

### Command Bus

El Command Bus es un patrón que desacopla la creación de comandos de su ejecución:

```php
interface CommandBus
{
    public function dispatch(object $command): void;
}

class SimpleCommandBus implements CommandBus
{
    private array $handlers = [];

    public function registerHandler(string $commandClass, callable $handler): void
    {
        $this->handlers[$commandClass] = $handler;
    }

    public function dispatch(object $command): void
    {
        $class = get_class($command);
        if (!isset($this->handlers[$class])) {
            throw new \RuntimeException("No handler registered for $class");
        }
        $handler = $this->handlers[$class];
        $handler($command);
    }
}
```

### Event Dispatcher

El Event Dispatcher es un patrón que permite la comunicación entre componentes sin acoplamiento directo:

```php
interface EventDispatcher
{
    public function dispatch(object $event): void;
    public function addListener(string $eventClass, callable $listener): void;
}
```

## Ejemplos Prácticos

### Añadir un Nuevo Value Object

Supongamos que queremos añadir un `ProductSku` (Stock Keeping Unit):

1. Crear el Value Object en `Domain/Product/ValueObject/ProductSku.php`:

```php
<?php
namespace App\Domain\Product\ValueObject;

final class ProductSku
{
    private string $value;

    public function __construct(string $value)
    {
        if (empty($value)) {
            throw new \InvalidArgumentException('SKU no puede estar vacío');
        }
        if (!preg_match('/^[A-Z0-9-]{3,15}$/', $value)) {
            throw new \InvalidArgumentException('SKU debe tener entre 3 y 15 caracteres alfanuméricos');
        }
        $this->value = $value;
    }

    public function value(): string
    {
        return $this->value;
    }

    public function equals(ProductSku $other): bool
    {
        return $this->value === $other->value();
    }
}
```

2. Modificar la entidad `Product` para incluir el nuevo Value Object:

```php
final class Product
{
    private ProductId $id;
    private ProductName $name;
    private ProductSku $sku; // Nuevo Value Object
    private string $description;
    private ProductPrice $price;
    private ProductStock $stock;
    private array $variants;

    public function __construct(
        ProductId $id,
        ProductName $name,
        ProductSku $sku, // Añadido al constructor
        string $description,
        ProductPrice $price,
        ProductStock $stock,
        array $variants = []
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->sku = $sku; // Asignación
        $this->description = $description;
        $this->price = $price;
        $this->stock = $stock;
        $this->variants = $variants;
    }

    public function getSku(): ProductSku // Nuevo getter
    {
        return $this->sku;
    }
    
    // Resto de getters y métodos...
}
```

3. Actualizar los comandos y handlers para incluir el SKU

### Crear un Nuevo Comando

Supongamos que queremos añadir un comando para asignar un producto a una categoría:

1. Crear el comando:

```php
<?php
namespace App\Application\Command\AssignProductToCategory;

class AssignProductToCategoryCommand
{
    public string $productId;
    public string $categoryId;

    public function __construct(string $productId, string $categoryId)
    {
        $this->productId = $productId;
        $this->categoryId = $categoryId;
    }
}
```

2. Crear el handler:

```php
<?php
namespace App\Application\Command\AssignProductToCategory;

use App\Domain\Product\Contract\ProductRepositoryInterface;
use App\Domain\Category\Contract\CategoryRepositoryInterface;
use App\Domain\Product\ValueObject\ProductId;
use App\Domain\Category\ValueObject\CategoryId;
use App\Domain\Product\Exception\ProductNotFoundException;
use App\Domain\Category\Exception\CategoryNotFoundException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use App\Domain\Product\Event\ProductAssignedToCategoryEvent;

class AssignProductToCategoryHandler
{
    private ProductRepositoryInterface $productRepository;
    private CategoryRepositoryInterface $categoryRepository;
    private ?EventDispatcherInterface $eventDispatcher;

    public function __construct(
        ProductRepositoryInterface $productRepository,
        CategoryRepositoryInterface $categoryRepository,
        ?EventDispatcherInterface $eventDispatcher = null
    ) {
        $this->productRepository = $productRepository;
        $this->categoryRepository = $categoryRepository;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function __invoke(AssignProductToCategoryCommand $command): void
    {
        $productId = new ProductId($command->productId);
        $categoryId = new CategoryId($command->categoryId);

        $product = $this->productRepository->find($productId);
        if (!$product) {
            throw new ProductNotFoundException("Producto con ID {$command->productId} no encontrado");
        }

        $category = $this->categoryRepository->find($categoryId);
        if (!$category) {
            throw new CategoryNotFoundException("Categoría con ID {$command->categoryId} no encontrada");
        }

        $product->assignToCategory($category);
        $this->productRepository->save($product);

        if ($this->eventDispatcher) {
            $this->eventDispatcher->dispatch(new ProductAssignedToCategoryEvent($product, $category));
        }
    }
}
```

3. Actualizar la entidad `Product` para soportar esta funcionalidad:

```php
// En Product.php
public function assignToCategory(Category $category): void
{
    // Lógica para asignar a categoría
    $this->categories[] = $category;
}
```

### Implementar un Nuevo Endpoint REST

Para implementar un endpoint que asigne un producto a una categoría:

```php
#[Route('/api/products/{productId}/categories/{categoryId}', name: 'api_assign_product_to_category', methods: ['POST'])]
public function assignToCategory(
    string $productId,
    string $categoryId,
    AssignProductToCategoryHandler $handler
): JsonResponse
{
    try {
        $command = new AssignProductToCategoryCommand($productId, $categoryId);
        $handler($command);
        
        return $this->json(['status' => 'ok'], 200);
    } catch (ProductNotFoundException | CategoryNotFoundException $exception) {
        return $this->json(['error' => $exception->getMessage()], 404);
    } catch (\Exception $exception) {
        return $this->json(['error' => 'Error interno del servidor'], 500);
    }
}
```

### Manejar un Nuevo Evento de Dominio

Para manejar el evento de asignación de producto a categoría:

1. Crear el evento:

```php
<?php
namespace App\Domain\Product\Event;

use App\Domain\Product\Product;
use App\Domain\Category\Category;

class ProductAssignedToCategoryEvent
{
    private Product $product;
    private Category $category;
    private \DateTimeImmutable $occurredOn;

    public function __construct(Product $product, Category $category)
    {
        $this->product = $product;
        $this->category = $category;
        $this->occurredOn = new \DateTimeImmutable();
    }

    public function getProduct(): Product
    {
        return $this->product;
    }

    public function getCategory(): Category
    {
        return $this->category;
    }

    public function getOccurredOn(): \DateTimeImmutable
    {
        return $this->occurredOn;
    }
}
```

2. Crear el listener:

```php
<?php
namespace App\Application\Event;

use App\Domain\Product\Event\ProductAssignedToCategoryEvent;
use Psr\Log\LoggerInterface;

class ProductAssignedToCategoryListener
{
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function __invoke(ProductAssignedToCategoryEvent $event): void
    {
        $product = $event->getProduct();
        $category = $event->getCategory();
        
        $this->logger->info(sprintf(
            'Producto "%s" (ID: %s) asignado a categoría "%s" (ID: %s)',
            $product->getName()->value(),
            $product->getId()->value(),
            $category->getName()->value(),
            $category->getId()->value()
        ));
        
        // Aquí podrían ir otras acciones como actualizar índices de búsqueda,
        // enviar notificaciones, etc.
    }
}
```

## Testing

### Testing por Capas

El sistema implementa tests unitarios y de integración para cada capa:

```
tests/
├── Application/               # Tests de la capa de aplicación
│   ├── Command/
│   │   └── CreateProductHandlerTest.php
│   └── Query/
│       └── GetProductHandlerTest.php
├── Domain/                    # Tests de la capa de dominio
│   └── Product/
│       ├── ProductTest.php
│       └── ValueObject/
│           └── ProductIdTest.php
├── Infrastructure/            # Tests de la capa de infraestructura
│   ├── Persistence/
│   │   └── DoctrineProductRepositoryTest.php
│   └── Email/
│       └── SmtpMailerTest.php
└── UI/                        # Tests de la capa de UI
    ├── Rest/
    │   └── Controller/
    │       └── ProductControllerTest.php
    └── CLI/
        └── Command/
            └── CreateProductCommandTest.php
```

### Ejemplos de Tests

Test de un Value Object:

```php
namespace App\Tests\Domain\Product\ValueObject;

use App\Domain\Product\ValueObject\ProductPrice;
use PHPUnit\Framework\TestCase;

class ProductPriceTest extends TestCase
{
    public function testValidPrice()
    {
        $price = new ProductPrice(19.99);
        $this->assertEquals(19.99, $price->amount());
    }

    public function testNegativePriceThrowsException()
    {
        $this->expectException(\InvalidArgumentException::class);
        new ProductPrice(-10);
    }

    public function testEquals()
    {
        $price1 = new ProductPrice(19.99);
        $price2 = new ProductPrice(19.99);
        $price3 = new ProductPrice(29.99);
        
        $this->assertTrue($price1->equals($price2));
        $this->assertFalse($price1->equals($price3));
    }

    public function testIncreaseByPercentage()
    {
        $price = new ProductPrice(100);
        $newPrice = $price->increaseByPercentage(10);
        
        $this->assertEquals(110, $newPrice->amount());
        // El objeto original no cambia (inmutabilidad)
        $this->assertEquals(100, $price->amount());
    }
}
```

Test de un Command Handler:

```php
namespace App\Tests\Application\Command\CreateProduct;

use App\Application\Command\CreateProduct\CreateProductCommand;
use App\Application\Command\CreateProduct\CreateProductHandler;
use App\Domain\Product\Contract\ProductRepositoryInterface;
use App\Domain\Product\Product;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class CreateProductHandlerTest extends TestCase
{
    private ProductRepositoryInterface $repository;
    private EventDispatcherInterface $eventDispatcher;
    private CreateProductHandler $handler;

    protected function setUp(): void
    {
        $this->repository = $this->createMock(ProductRepositoryInterface::class);
        $this->eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $this->handler = new CreateProductHandler(
            $this->repository,
            $this->eventDispatcher
        );
    }

    public function testCreateProduct()
    {
        // Arrange
        $command = new CreateProductCommand(
            'Camiseta',
            'Camiseta de algodón',
            19.99,
            100,
            [
                [
                    'size' => 'S',
                    'color' => 'Rojo',
                    'price' => 19.99,
                    'stock' => 30,
                    'imageUrl' => 'https://example.com/camiseta-roja-s.jpg'
                ]
            ]
        );

        // Assert
        $this->repository
            ->expects($this->once())
            ->method('save')
            ->with($this->callback(function (Product $product) {
                return $product->getName()->value() === 'Camiseta'
                    && $product->getDescription() === 'Camiseta de algodón'
                    && $product->getPrice()->amount() === 19.99
                    && $product->getStock()->value() === 100
                    && count($product->getVariants()) === 1;
            }));

        $this->eventDispatcher
            ->expects($this->once())
            ->method('dispatch')
            ->with($this->callback(function ($event) {
                return $event instanceof \App\Domain\Product\Event\ProductCreatedEvent
                    && $event->getProduct()->getName()->value() === 'Camiseta';
            }));

        // Act
        ($this->handler)($command);
    }
}
```

## Consideraciones Avanzadas

### Rendimiento

- **Proyecciones de lectura**: Para mejorar el rendimiento de lectura, se pueden implementar proyecciones específicas para cada caso de uso.
- **Caché**: Utilizar caché para consultas frecuentes o costosas.
- **Consultas optimizadas**: Las consultas de Doctrine pueden optimizarse con DQL directo o QueryBuilder.

### Escalabilidad

- **Microservicios**: El diseño modular facilita la migración a microservicios si es necesario.
- **Asincronía**: Los eventos de dominio pueden procesarse de forma asíncrona mediante colas.
- **CQRS avanzado**: Separar completamente los modelos de lectura y escritura.

### Mantenibilidad

- **Testing automatizado**: La estructura por capas facilita el testing unitario.
- **Documentación**: Los nombres descriptivos y la estructura clara actúan como documentación viva.
- **Consistencia**: Seguir los mismos patrones en todos los módulos.

## Glosario

- **Agregado**: Un conjunto de objetos de dominio que se tratan como una unidad.
- **Value Object**: Un objeto inmutable definido por sus atributos, no por una identidad.
- **Entidad**: Un objeto con identidad que puede cambiar a lo largo del tiempo.
- **Repositorio**: Un objeto que abstrae el acceso a datos de entidades.
- **Comando**: Una intención de modificar el estado del sistema.
- **Query**: Una intención de obtener información del sistema.
- **Evento de dominio**: Una notificación de algo significativo que ha ocurrido en el dominio.
- **Handler**: Un objeto que procesa un comando o query.
- **Adaptador**: Una implementación específica de un puerto.
- **Puerto**: Una interfaz que define cómo el dominio interactúa con el exterior.
- **CQRS**: Command Query Responsibility Segregation, separación de operaciones de lectura y escritura.
- **DDD**: Domain-Driven Design, enfoque de diseño centrado en el dominio.
- **Arquitectura Hexagonal**: Patrón arquitectónico que separa el núcleo de la aplicación de los detalles técnicos.
