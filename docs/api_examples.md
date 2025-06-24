# Ejemplos Prácticos de Uso de la API

Este documento proporciona ejemplos concretos de cómo interactuar con el sistema de gestión de inventario a través de su API REST.

## Requisitos

- Una herramienta para realizar peticiones HTTP como [Postman](https://www.postman.com/), [Insomnia](https://insomnia.rest/) o `curl`
- El servidor del proyecto ejecutándose (normalmente en `http://localhost:8000`)

## Operaciones CRUD para Productos

### 1. Crear un Nuevo Producto

**Endpoint**: `POST /api/products`

**Headers**:
```
Content-Type: application/json
```

**Cuerpo de la petición**:
```json
{
  "name": "Camiseta de Algodón",
  "description": "Camiseta 100% algodón orgánico, ideal para el verano",
  "price": 24.99,
  "stock": 100,
  "variants": [
    {
      "size": "S",
      "color": "Blanco",
      "price": 24.99,
      "stock": 30,
      "imageUrl": "https://example.com/images/camiseta-blanca-s.jpg"
    },
    {
      "size": "M",
      "color": "Blanco",
      "price": 24.99,
      "stock": 40,
      "imageUrl": "https://example.com/images/camiseta-blanca-m.jpg"
    },
    {
      "size": "L",
      "color": "Blanco",
      "price": 24.99,
      "stock": 30,
      "imageUrl": "https://example.com/images/camiseta-blanca-l.jpg"
    }
  ]
}
```

**Respuesta exitosa** (Código 201 Created):
```json
{
  "status": "Product created",
  "id": "9f8d7c6b-5a4e-3c2d-1b0a-9f8e7d6c5b4a"
}
```

### 2. Obtener un Producto por ID

**Endpoint**: `GET /api/products/{id}`

**Ejemplo**: `GET /api/products/9f8d7c6b-5a4e-3c2d-1b0a-9f8e7d6c5b4a`

**Respuesta exitosa** (Código 200 OK):
```json
{
  "id": "9f8d7c6b-5a4e-3c2d-1b0a-9f8e7d6c5b4a",
  "name": "Camiseta de Algodón",
  "description": "Camiseta 100% algodón orgánico, ideal para el verano",
  "price": 24.99,
  "stock": 100,
  "variants": [
    {
      "size": "S",
      "color": "Blanco",
      "price": 24.99,
      "stock": 30,
      "imageUrl": "https://example.com/images/camiseta-blanca-s.jpg"
    },
    {
      "size": "M",
      "color": "Blanco",
      "price": 24.99,
      "stock": 40,
      "imageUrl": "https://example.com/images/camiseta-blanca-m.jpg"
    },
    {
      "size": "L",
      "color": "Blanco",
      "price": 24.99,
      "stock": 30,
      "imageUrl": "https://example.com/images/camiseta-blanca-l.jpg"
    }
  ]
}
```

**Respuesta de error** (Código 404 Not Found):
```json
{
  "error": "Producto con ID 9f8d7c6b-5a4e-3c2d-1b0a-9f8e7d6c5b4a no encontrado"
}
```

### 3. Listar Todos los Productos

**Endpoint**: `GET /api/products`

**Parámetros opcionales**:
- `page`: Número de página (por defecto: 1)
- `limit`: Productos por página (por defecto: 10)

**Ejemplo**: `GET /api/products?page=1&limit=5`

**Respuesta exitosa** (Código 200 OK):
```json
{
  "products": [
    {
      "id": "9f8d7c6b-5a4e-3c2d-1b0a-9f8e7d6c5b4a",
      "name": "Camiseta de Algodón",
      "price": 24.99,
      "stock": 100,
      "variants": [
        {
          "size": "S",
          "color": "Blanco",
          "price": 24.99
        },
        {
          "size": "M",
          "color": "Blanco",
          "price": 24.99
        },
        {
          "size": "L",
          "color": "Blanco",
          "price": 24.99
        }
      ]
    },
    // Más productos...
  ],
  "page": 1,
  "limit": 5,
  "total": 42,
  "totalPages": 9
}
```

### 4. Actualizar un Producto

**Endpoint**: `PUT /api/products/{id}`

**Headers**:
```
Content-Type: application/json
```

**Ejemplo**: `PUT /api/products/9f8d7c6b-5a4e-3c2d-1b0a-9f8e7d6c5b4a`

**Cuerpo de la petición** (solo los campos a actualizar):
```json
{
  "name": "Camiseta Premium de Algodón",
  "price": 29.99,
  "variants": [
    {
      "size": "S",
      "color": "Blanco",
      "price": 29.99,
      "stock": 25,
      "imageUrl": "https://example.com/images/camiseta-premium-blanca-s.jpg"
    },
    {
      "size": "M",
      "color": "Blanco",
      "price": 29.99,
      "stock": 35,
      "imageUrl": "https://example.com/images/camiseta-premium-blanca-m.jpg"
    },
    {
      "size": "L",
      "color": "Blanco",
      "price": 29.99,
      "stock": 25,
      "imageUrl": "https://example.com/images/camiseta-premium-blanca-l.jpg"
    }
  ]
}
```

**Respuesta exitosa** (Código 200 OK):
```json
{
  "status": "Product updated"
}
```

### 5. Eliminar un Producto

**Endpoint**: `DELETE /api/products/{id}`

**Ejemplo**: `DELETE /api/products/9f8d7c6b-5a4e-3c2d-1b0a-9f8e7d6c5b4a`

**Respuesta exitosa** (Código 200 OK):
```json
{
  "status": "Product deleted"
}
```

## Ejemplos con cURL

### Crear un producto
```bash
curl -X POST http://localhost:8000/api/products \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Pantalón Vaquero",
    "description": "Pantalón vaquero clásico de alta calidad",
    "price": 49.99,
    "stock": 75,
    "variants": [
      {
        "size": "38",
        "color": "Azul",
        "price": 49.99,
        "stock": 25,
        "imageUrl": "https://example.com/images/vaquero-azul-38.jpg"
      },
      {
        "size": "40",
        "color": "Azul",
        "price": 49.99,
        "stock": 25,
        "imageUrl": "https://example.com/images/vaquero-azul-40.jpg"
      },
      {
        "size": "42",
        "color": "Azul",
        "price": 49.99,
        "stock": 25,
        "imageUrl": "https://example.com/images/vaquero-azul-42.jpg"
      }
    ]
  }'
```

### Obtener un producto
```bash
curl -X GET http://localhost:8000/api/products/9f8d7c6b-5a4e-3c2d-1b0a-9f8e7d6c5b4a
```

### Listar productos
```bash
curl -X GET "http://localhost:8000/api/products?page=1&limit=10"
```

### Actualizar un producto
```bash
curl -X PUT http://localhost:8000/api/products/9f8d7c6b-5a4e-3c2d-1b0a-9f8e7d6c5b4a \
  -H "Content-Type: application/json" \
  -d '{
    "price": 44.99,
    "stock": 100
  }'
```

### Eliminar un producto
```bash
curl -X DELETE http://localhost:8000/api/products/9f8d7c6b-5a4e-3c2d-1b0a-9f8e7d6c5b4a
```

## Manejando Errores

El API utiliza códigos de estado HTTP estándar:

- **200 OK**: Operación completada con éxito
- **201 Created**: Recurso creado correctamente
- **400 Bad Request**: Error en la solicitud (datos inválidos)
- **404 Not Found**: Recurso no encontrado
- **500 Internal Server Error**: Error interno del servidor

Los errores devuelven un objeto JSON con un mensaje descriptivo:

```json
{
  "error": "Descripción del error"
}
```

## Autenticación

Esta documentación cubre el API básico sin autenticación. En un entorno de producción, se implementaría autenticación mediante JWT, OAuth2 u otro mecanismo similar.
