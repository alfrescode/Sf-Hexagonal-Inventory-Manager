# Laravel vs Symfony con Arquitectura Hexagonal - Guía Comparativa

Este documento proporciona una comparación entre Laravel (un framework MVC tradicional) y el enfoque utilizado en este proyecto (Symfony con Arquitectura Hexagonal). Está especialmente diseñado para desarrolladores que vienen de Laravel y quieren entender este nuevo enfoque.

## Diferencias en la Estructura de Directorios

### Laravel (MVC Tradicional)

```
app/
  Http/
    Controllers/    # Controladores
  Models/           # Modelos Eloquent
  Providers/        # Service Providers
  Events/           # Eventos
  Listeners/        # Listeners
database/
  migrations/       # Migraciones
resources/
  views/            # Vistas (en aplicaciones con frontend)
routes/
  api.php           # Definición de rutas API
  web.php           # Definición de rutas web
```

### Este Proyecto (Symfony + Arquitectura Hexagonal)

```
src/
  Domain/           # Lógica de negocio pura
    Product/
      ValueObject/  # Objetos de valor (inmutables)
      Contract/     # Interfaces (puertos)
      Event/        # Eventos de dominio
  Application/      # Casos de uso
    Command/        # Comandos (modifican estado)
    Query/          # Consultas (leen datos)
    Event/          # Listeners de eventos
  Infrastructure/   # Implementaciones técnicas
    Persistence/    # Acceso a datos
    Email/          # Servicios de email
  UI/               # Interfaces de usuario
    Rest/           # API REST
    CLI/            # Comandos de consola
```

## Ejemplos Comparativos

### 1. Crear un Nuevo Producto

#### En Laravel:

```php
// app/Http/Controllers/ProductController.php
public function store(Request $request)
{
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'description' => 'required|string',
        'price' => 'required|numeric|min:0',
        'stock' => 'required|integer|min:0',
    ]);
    
    $product = new Product($validated);
    $product->save();
    
    event(new ProductCreated($product));
    
    return response()->json($product, 201);
}
```

#### En este Proyecto:

```php
// src/UI/Rest/Controller/ProductController.php
public function create(Request $request, CommandBusInterface $commandBus)
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

// src/Application/Command/CreateProduct/CreateProductHandler.php
public function __invoke(CreateProductCommand $command): void
{
    $product = new Product(
        new ProductId(Uuid::v4()->toRfc4122()),
        new ProductName($command->name),
        $command->description,
        new ProductPrice($command->price),
        new ProductStock($command->stock),
        array_map(function ($variant) {
            return new ProductVariant(/* ... */);
        }, $command->variants)
    );
    
    $this->repository->save($product);
    
    if ($this->eventDispatcher) {
        $this->eventDispatcher->dispatch(new ProductCreatedEvent($product));
    }
}
```

### 2. Obtener un Producto

#### En Laravel:

```php
// app/Http/Controllers/ProductController.php
public function show($id)
{
    $product = Product::findOrFail($id);
    return response()->json($product);
}
```

#### En este Proyecto:

```php
// src/UI/Rest/Controller/ProductController.php
public function getProduct(string $id, QueryBusInterface $queryBus)
{
    try {
        $productDTO = $queryBus->dispatch(new GetProductQuery($id));
        return new JsonResponse($productDTO);
    } catch (ProductNotFoundException $e) {
        return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_NOT_FOUND);
    }
}

// src/Application/Query/GetProduct/GetProductHandler.php
public function __invoke(GetProductQuery $query): ProductDTO
{
    $product = $this->repository->find(new ProductId($query->id));
    
    if (!$product) {
        throw new ProductNotFoundException("Producto con ID {$query->id} no encontrado");
    }
    
    // Mapear las variantes a DTOs...
    
    return new ProductDTO(
        $product->getId()->value(),
        $product->getName()->value(),
        $product->getDescription(),
        $product->getPrice()->value(),
        $product->getStock()->value(),
        $variantDTOs
    );
}
```

## Conceptos Clave para Desarrolladores de Laravel

### 1. Value Objects vs. Atributos de Modelo

#### En Laravel:
```php
// Modelo con atributos simples
class Product extends Model
{
    protected $fillable = ['name', 'price', 'stock'];
    
    // El precio es simplemente un número
    public function incrementPrice($amount)
    {
        $this->price += $amount;
        $this->save();
    }
}
```

#### En este Proyecto:
```php
// Entidad con Value Objects
class Product
{
    private ProductName $name;
    private ProductPrice $price;
    private ProductStock $stock;
    
    // El precio es un objeto con su propia lógica
    public function setPrice(ProductPrice $price): void
    {
        $this->price = $price;
    }
}

// Value Object
final class ProductPrice
{
    private float $value;
    
    public function __construct(float $price)
    {
        if ($price < 0) {
            throw new \InvalidArgumentException("El precio no puede ser negativo.");
        }
        $this->value = $price;
    }
    
    public function value(): float
    {
        return $this->value;
    }
}
```

### 2. Repositorios vs. Eloquent

#### En Laravel:
```php
// Acceso directo a la base de datos con Eloquent
$products = Product::where('price', '>', 100)->get();
$product = Product::find($id);
$product->stock = 20;
$product->save();
```

#### En este Proyecto:
```php
// Interfaces de repositorio
interface ProductRepositoryInterface
{
    public function find(ProductId $id): ?Product;
    public function findAll(int $page = 1, int $limit = 10): array;
    public function save(Product $product): void;
    public function delete(ProductId $id): void;
}

// Uso a través de la interfaz
$product = $this->repository->find(new ProductId($id));
$product->setStock(new ProductStock(20));
$this->repository->save($product);
```

### 3. CQRS vs. Controladores Tradicionales

#### En Laravel:
Los controladores manejan tanto la lectura como la escritura.

```php
class ProductController extends Controller
{
    public function index() { /* Listar productos */ }
    public function show($id) { /* Mostrar un producto */ }
    public function store(Request $request) { /* Crear producto */ }
    public function update(Request $request, $id) { /* Actualizar producto */ }
    public function destroy($id) { /* Eliminar producto */ }
}
```

#### En este Proyecto:
Se separan comandos (escritura) y consultas (lectura).

```php
// Comandos - Modifican estado
class CreateProductCommand { /* ... */ }
class UpdateProductCommand { /* ... */ }
class DeleteProductCommand { /* ... */ }

// Queries - Leen datos
class GetProductQuery { /* ... */ }
class ListProductsQuery { /* ... */ }
```

## Ventajas de la Arquitectura Hexagonal para Desarrolladores de Laravel

1. **Testabilidad mejorada**: Puedes probar la lógica de negocio sin depender de la base de datos o frameworks.

2. **Mantenimiento a largo plazo**: Al tener capas bien definidas, es más fácil realizar cambios sin afectar otras partes del sistema.

3. **Flexibilidad tecnológica**: Puedes cambiar la base de datos, el framework o incluso el lenguaje de programación sin alterar la lógica de negocio.

4. **Dominio rico**: Los objetos de dominio encapsulan reglas de negocio, evitando la dispersión de lógica.

5. **Claridad de intención**: Cada clase tiene una única responsabilidad bien definida.

## Desventajas Potenciales

1. **Curva de aprendizaje**: Es más complejo de entender al principio.

2. **Más código inicial**: Requiere escribir más código desde el principio.

3. **Abstracción adicional**: Añade capas que pueden parecer innecesarias para aplicaciones pequeñas.

## Consejos para la Transición

1. **Empieza por entender el dominio**: Concéntrate primero en las entidades y objetos de valor.

2. **Avanza por capas**: Primero dominio, luego aplicación, finalmente infraestructura.

3. **No confundas DTOs con Entidades**: Los DTOs son para transferir datos, las entidades contienen lógica de negocio.

4. **Piensa en términos de comportamiento**: No en términos de datos como en Laravel.

5. **Aprovecha la inyección de dependencias**: Symfony tiene un contenedor de servicios potente.

---

Este documento es una guía introductoria. Para profundizar, se recomienda estudiar los principios de Domain-Driven Design (DDD) y la Arquitectura Hexagonal más a fondo.
