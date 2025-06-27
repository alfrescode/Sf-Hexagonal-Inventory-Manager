# Comparativa: Laravel vs Symfony con Arquitectura Hexagonal

Este documento ofrece una comparación detallada entre Laravel, el framework que probablemente ya conoces bien, y Symfony implementando Domain-Driven Design (DDD) con Arquitectura Hexagonal. El objetivo es facilitar la comprensión de las diferencias clave y ayudarte a trasladar tus conocimientos de Laravel a este nuevo paradigma.

## Tabla de Contenidos

1. [Filosofía y Enfoque](#filosofía-y-enfoque)
2. [Estructura de Directorios](#estructura-de-directorios)
3. [Modelos vs Entidades de Dominio](#modelos-vs-entidades-de-dominio)
4. [Controladores](#controladores)
5. [ORM: Eloquent vs Doctrine](#orm-eloquent-vs-doctrine)
6. [Manejo de Eventos](#manejo-de-eventos)
7. [Validación](#validación)
8. [Inyección de Dependencias](#inyección-de-dependencias)
9. [Ejemplos Comparativos](#ejemplos-comparativos)
10. [Ventajas y Desventajas](#ventajas-y-desventajas)
11. [Cuándo Usar Cada Enfoque](#cuándo-usar-cada-enfoque)

## Filosofía y Enfoque

### Laravel

- **Enfoque**: Simplicidad, convenciones sobre configuración, desarrollo rápido.
- **Estructura**: Monolítica, con carpetas predefinidas por funcionalidad técnica (controllers, models, etc.)
- **Paradigma**: Active Record (los modelos conocen cómo persistirse).
- **Acoplamiento**: Los modelos están acoplados a la base de datos.

### Symfony con Arquitectura Hexagonal

- **Enfoque**: Separación de responsabilidades, independencia de infraestructura, flexibilidad a largo plazo.
- **Estructura**: Por capas según responsabilidad de negocio (dominio, aplicación, infraestructura, UI).
- **Paradigma**: Data Mapper (la persistencia está separada de los modelos).
- **Acoplamiento**: El dominio no conoce a la infraestructura, solo viceversa.

## Estructura de Directorios

### Laravel (Estándar)

```
app/
├── Console/              # Comandos de consola
├── Http/
│   ├── Controllers/      # Controladores HTTP
│   ├── Middleware/       # Middleware HTTP
│   └── Requests/         # Form Requests para validación
├── Models/               # Modelos Eloquent
├── Providers/            # Service Providers
├── Events/               # Eventos de la aplicación
├── Listeners/            # Listeners para eventos
├── Jobs/                 # Trabajos en cola
└── Exceptions/           # Manejadores de excepciones
```

### Symfony con Arquitectura Hexagonal

```
src/
├── Domain/               # Capa de Dominio - lógica de negocio
│   └── Product/
│       ├── Product.php   # Entidad raíz 
│       ├── ValueObject/  # Value Objects (inmutables)
│       └── Repository/   # Interfaces de repositorios
├── Application/          # Casos de uso de la aplicación
│   ├── Command/          # Comandos (escritura)
│   ├── Query/            # Consultas (lectura)
│   └── Event/            # Listeners de eventos
├── Infrastructure/       # Implementaciones técnicas
│   ├── Persistence/      # Repositorios concretos
│   └── Service/          # Servicios externos
└── UI/                   # Interfaces de usuario
    ├── Rest/             # API REST
    └── Console/          # Comandos CLI
```

## Modelos vs Entidades de Dominio

### Laravel: Modelos Eloquent

```php
class Product extends Model
{
    protected $fillable = ['name', 'price', 'stock'];
    
    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }
    
    public function decreaseStock(int $quantity)
    {
        $this->stock -= $quantity;
        $this->save();
    }
}
```

**Características**:
- Hereda de Eloquent Model
- Conoce sobre la base de datos (tabla, columnas)
- Incluye relaciones con otros modelos
- Mezcla lógica de negocio y persistencia
- Usa getters/setters mágicos

### Symfony + DDD: Entidades de Dominio

```php
namespace App\Domain\Product;

use App\Domain\Product\ValueObject\ProductId;
use App\Domain\Product\ValueObject\ProductName;
use App\Domain\Product\ValueObject\ProductPrice;
use App\Domain\Product\ValueObject\ProductStock;

final class Product
{
    private ProductId $id;
    private ProductName $name;
    private ProductPrice $price;
    private ProductStock $stock;
    private array $variants;
    
    public function __construct(
        ProductId $id,
        ProductName $name,
        ProductPrice $price,
        ProductStock $stock,
        array $variants = []
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->price = $price;
        $this->stock = $stock;
        $this->variants = $variants;
    }
    
    public function decreaseStock(int $quantity): void
    {
        $this->stock = $this->stock->decrease($quantity);
    }
    
    // Getters explícitos
    public function getId(): ProductId
    {
        return $this->id;
    }
    
    public function getName(): ProductName
    {
        return $this->name;
    }
    
    // Resto de getters...
}
```

**Características**:
- Clase independiente sin herencia
- No sabe nada sobre persistencia
- Usa Value Objects para encapsular validaciones
- Lógica de negocio pura
- Getters/setters explícitos

## Controladores

### Laravel

```php
class ProductController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
        ]);
        
        $product = Product::create($validated);
        
        // Opcional: disparar evento
        event(new ProductCreated($product));
        
        return response()->json($product, 201);
    }
}
```

**Características**:
- Validación directamente en el controlador
- Creación directa del modelo
- Dispara eventos directamente
- Mezcla responsabilidades (validación, creación, respuesta)

### Symfony + DDD + CQRS

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
}
```

**Características**:
- Controlador enfocado solo en HTTP
- Delega validación a DTOs o Value Objects
- Crea comandos y los pasa a handlers
- Separación clara de responsabilidades

## ORM: Eloquent vs Doctrine

### Laravel: Eloquent (Active Record)

```php
// Crear un producto
$product = new Product();
$product->name = 'Camiseta';
$product->price = 19.99;
$product->stock = 100;
$product->save();

// Buscar un producto
$product = Product::find(1);

// Actualizar un producto
$product->price = 29.99;
$product->save();

// Eliminar un producto
$product->delete();

// Consulta con relaciones
$products = Product::with('variants')
    ->where('price', '>', 10)
    ->orderBy('name')
    ->get();
```

**Características**:
- Sintaxis fluida y sencilla
- El modelo sabe cómo persistirse
- Relaciones definidas en el modelo
- Query builder integrado en el modelo

### Symfony: Doctrine (Data Mapper)

```php
// Repositorio en infraestructura
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
    
    public function findByPriceRange(float $min, float $max): array
    {
        $qb = $this->em->createQueryBuilder();
        $qb->select('p')
           ->from(ProductEntity::class, 'p')
           ->where('p.price >= :min')
           ->andWhere('p.price <= :max')
           ->setParameter('min', $min)
           ->setParameter('max', $max)
           ->orderBy('p.name', 'ASC');
        
        $entities = $qb->getQuery()->getResult();
        return array_map([ProductEntityMapper::class, 'toDomain'], $entities);
    }
}

// Uso en un handler
class CreateProductHandler
{
    public function __invoke(CreateProductCommand $command): void
    {
        $product = new Product(
            new ProductId(Uuid::v4()->toRfc4122()),
            new ProductName($command->name),
            new ProductPrice($command->price),
            new ProductStock($command->stock)
        );
        
        $this->repository->save($product);
    }
}
```

**Características**:
- Separación entre modelo de dominio y modelo de persistencia
- Repositorios encapsulan lógica de acceso a datos
- EntityManager gestiona transacciones y persistencia
- Mapeo explícito entre modelos de dominio y entidades de persistencia

## Manejo de Eventos

### Laravel

```php
// Definir evento
class ProductCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $product;
    
    public function __construct(Product $product)
    {
        $this->product = $product;
    }
}

// Definir listener
class SendProductCreatedNotification
{
    public function handle(ProductCreated $event)
    {
        Mail::to('pepe@up-spain.com')->send(
            new ProductCreatedMail($event->product)
        );
    }
}

// Registrar en EventServiceProvider
protected $listen = [
    ProductCreated::class => [
        SendProductCreatedNotification::class,
    ],
];

// Disparar evento
event(new ProductCreated($product));
```

**Características**:
- Eventos como clases PHP
- Listeners definidos en array de configuración
- Sistema de eventos integrado en el framework
- Posibilidad de eventos en cola

### Symfony + DDD

```php
// Evento de dominio
namespace App\Domain\Product\Event;

use App\Domain\Product\Product;

class ProductCreatedEvent
{
    private Product $product;
    private \DateTimeImmutable $occurredOn;

    public function __construct(Product $product)
    {
        $this->product = $product;
        $this->occurredOn = new \DateTimeImmutable();
    }

    public function getProduct(): Product
    {
        return $this->product;
    }

    public function getOccurredOn(): \DateTimeImmutable
    {
        return $this->occurredOn;
    }
}

// Listener en aplicación
namespace App\Application\Event;

use App\Domain\Product\Event\ProductCreatedEvent;
use App\Infrastructure\Email\EmailSenderInterface;

class ProductCreatedListener
{
    private EmailSenderInterface $emailSender;
    
    public function __construct(EmailSenderInterface $emailSender)
    {
        $this->emailSender = $emailSender;
    }
    
    public function __invoke(ProductCreatedEvent $event): void
    {
        $product = $event->getProduct();
        $this->emailSender->send(
            'pepe@up-spain.com',
            'Nuevo producto creado',
            sprintf('El producto "%s" ha sido creado.', $product->getName()->value())
        );
    }
}

// Configuración en services.yaml
services:
    App\Application\Event\ProductCreatedListener:
        tags:
            - { name: kernel.event_listener, event: App\Domain\Product\Event\ProductCreatedEvent }
```

**Características**:
- Eventos de dominio inmutables
- Listeners como servicios
- Configuración mediante tags o atributos
- Separación clara entre eventos de dominio y sistema

## Validación

### Laravel

```php
// En el controlador
public function store(Request $request)
{
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'price' => 'required|numeric|min:0',
        'stock' => 'required|integer|min:0',
    ]);
    
    // O usando Form Request
    $validated = $request->validated();
    
    $product = Product::create($validated);
    
    return response()->json($product, 201);
}

// Form Request
class CreateProductRequest extends FormRequest
{
    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
        ];
    }
}
```

**Características**:
- Validación centralizada en controladores o Form Requests
- Reglas de validación declarativas
- Mensajes de error personalizables
- Validación antes de la lógica de negocio

### Symfony + DDD

```php
// Value Objects con validación
namespace App\Domain\Product\ValueObject;

final class ProductName
{
    private string $value;
    
    public function __construct(string $value)
    {
        if (empty($value)) {
            throw new \InvalidArgumentException('El nombre del producto no puede estar vacío');
        }
        
        if (strlen($value) > 255) {
            throw new \InvalidArgumentException('El nombre del producto no puede tener más de 255 caracteres');
        }
        
        $this->value = $value;
    }
    
    public function value(): string
    {
        return $this->value;
    }
}

// DTO para validación en capa UI
namespace App\UI\Rest\DTO;

class CreateProductRequest
{
    public string $name;
    public string $description;
    public float $price;
    public int $stock;
    public array $variants;
    
    public static function fromArray(array $data): self
    {
        $dto = new self();
        
        if (!isset($data['name']) || empty($data['name'])) {
            throw new \InvalidArgumentException('El nombre es requerido');
        }
        $dto->name = $data['name'];
        
        $dto->description = $data['description'] ?? '';
        
        if (!isset($data['price']) || !is_numeric($data['price']) || $data['price'] < 0) {
            throw new \InvalidArgumentException('El precio debe ser un número positivo');
        }
        $dto->price = (float) $data['price'];
        
        if (!isset($data['stock']) || !is_numeric($data['stock']) || $data['stock'] < 0) {
            throw new \InvalidArgumentException('El stock debe ser un número entero positivo');
        }
        $dto->stock = (int) $data['stock'];
        
        $dto->variants = $data['variants'] ?? [];
        
        return $dto;
    }
}
```

**Características**:
- Validación en múltiples capas
- Value Objects validan invariantes del dominio
- DTOs validan formato de entrada
- Excepciones específicas para cada tipo de error

## Inyección de Dependencias

### Laravel

```php
class ProductController extends Controller
{
    private ProductService $productService;
    
    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }
    
    public function store(Request $request)
    {
        $validated = $request->validate([/* ... */]);
        $product = $this->productService->createProduct($validated);
        return response()->json($product, 201);
    }
}

// Binding en AppServiceProvider
public function register()
{
    $this->app->bind(ProductRepositoryInterface::class, EloquentProductRepository::class);
}
```

**Características**:
- Container de servicios integrado
- Resolución automática de dependencias
- Bindings configurables en Service Providers
- Fácil de usar pero menos explícito

### Symfony + DDD

```php
// services.yaml
services:
    _defaults:
        autowire: true
        autoconfigure: true
    
    App\UI\Rest\Controller\:
        resource: '../src/UI/Rest/Controller/'
        tags: ['controller.service_arguments']
    
    App\Domain\Product\Contract\ProductRepositoryInterface:
        class: App\Infrastructure\Persistence\Doctrine\Repository\DoctrineProductRepository

// Uso en el código
class CreateProductHandler
{
    private ProductRepositoryInterface $repository;
    private EventDispatcherInterface $eventDispatcher;
    
    public function __construct(
        ProductRepositoryInterface $repository,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->repository = $repository;
        $this->eventDispatcher = $eventDispatcher;
    }
    
    // ...
}
```

**Características**:
- Configuración explícita de servicios
- Autowiring para inyección automática
- Interfaces claramente vinculadas a implementaciones
- Más verboso pero más explícito

## Ejemplos Comparativos

### Crear un Producto

#### Laravel

```php
// ProductController.php
public function store(Request $request)
{
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'description' => 'nullable|string',
        'price' => 'required|numeric|min:0',
        'stock' => 'required|integer|min:0',
        'variants' => 'nullable|array',
    ]);
    
    $product = Product::create([
        'name' => $validated['name'],
        'description' => $validated['description'] ?? '',
        'price' => $validated['price'],
        'stock' => $validated['stock'],
    ]);
    
    if (!empty($validated['variants'])) {
        foreach ($validated['variants'] as $variant) {
            $product->variants()->create([
                'size' => $variant['size'] ?? '',
                'color' => $variant['color'] ?? '',
                'price' => $variant['price'],
                'stock' => $variant['stock'],
                'image_url' => $variant['imageUrl'] ?? '',
            ]);
        }
    }
    
    event(new ProductCreated($product));
    
    return response()->json($product->load('variants'), 201);
}
```

#### Symfony + DDD + CQRS

```php
// ProductController.php
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

// CreateProductHandler.php
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
```

### Consultar un Producto

#### Laravel

```php
// ProductController.php
public function show($id)
{
    $product = Product::with('variants')->findOrFail($id);
    return response()->json($product);
}
```

#### Symfony + DDD + CQRS

```php
// ProductController.php
#[Route('/api/products/{id}', name: 'api_get_product', methods: ['GET'])]
public function get(string $id, GetProductHandler $handler): JsonResponse
{
    try {
        $query = new GetProductQuery($id);
        $product = $handler($query);
        
        return $this->json($product);
    } catch (ProductNotFoundException $exception) {
        return $this->json(['error' => $exception->getMessage()], 404);
    }
}

// GetProductHandler.php
public function __invoke(GetProductQuery $query): array
{
    $id = new ProductId($query->id);
    $product = $this->repository->find($id);
    
    if (!$product) {
        throw new ProductNotFoundException("Producto con ID {$query->id} no encontrado");
    }
    
    return [
        'id' => $product->getId()->value(),
        'name' => $product->getName()->value(),
        'description' => $product->getDescription(),
        'price' => $product->getPrice()->amount(),
        'stock' => $product->getStock()->value(),
        'variants' => array_map(function (ProductVariant $variant) {
            return [
                'size' => $variant->getSize(),
                'color' => $variant->getColor(),
                'price' => $variant->getPrice()->amount(),
                'stock' => $variant->getStock()->value(),
                'imageUrl' => $variant->getImageUrl(),
            ];
        }, $product->getVariants()),
    ];
}
```

## Ventajas y Desventajas

### Laravel (Enfoque Tradicional)

**Ventajas**:
- Rápido desarrollo inicial
- Menor cantidad de código y archivos
- Curva de aprendizaje más suave
- Excelente para aplicaciones CRUD simples
- Gran ecosistema de paquetes

**Desventajas**:
- Difícil separar lógica de negocio de infraestructura
- Pruebas unitarias más complejas (dependencias a DB)
- Mayor acoplamiento entre componentes
- Puede volverse difícil de mantener en aplicaciones complejas
- Menos control sobre la lógica del dominio

### Symfony + DDD + Arquitectura Hexagonal

**Ventajas**:
- Clara separación de responsabilidades
- Dominio aislado y protegido
- Fácil de testear unitariamente
- Flexible para cambiar tecnologías
- Mejor para proyectos complejos a largo plazo
- Escalable a microservicios

**Desventajas**:
- Mayor complejidad inicial
- Más archivos y código boilerplate
- Curva de aprendizaje pronunciada
- Puede ser excesivo para aplicaciones simples
- Desarrollo inicial más lento

## Cuándo Usar Cada Enfoque

### Usar Laravel Tradicional

- Aplicaciones CRUD simples
- Prototipos y MVPs
- Proyectos con plazos muy ajustados
- Equipos pequeños o con experiencia limitada
- Aplicaciones con requisitos de negocio estables

### Usar Symfony + DDD + Arquitectura Hexagonal

- Aplicaciones de dominio complejo
- Proyectos empresariales a largo plazo
- Cuando la lógica de negocio es crítica
- Equipos grandes con roles especializados
- Cuando se prevén cambios tecnológicos
- Sistemas que evolucionarán a microservicios
