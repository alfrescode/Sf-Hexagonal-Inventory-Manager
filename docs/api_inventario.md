# API de Gestión de Inventario

## Productos

### Crear un producto
- **Método**: POST
- **Ruta**: `/api/products`
- **Body**:
```json
{
  "name": "Producto de ejemplo",
  "description": "Descripción del producto",
  "price": 99.99,
  "stock": 10,
  "variants": [
    {
      "size": "L",
      "color": "Rojo",
      "price": 99.99,
      "stock": 5,
      "imageUrl": "http://ejemplo.com/imagen.jpg"
    }
  ]
}
```
- **Respuesta**: 201 Created
```json
{
  "status": "ok"
}
```

### Obtener un producto
- **Método**: GET
- **Ruta**: `/api/products/{id}`
- **Respuesta**: 200 OK
```json
{
  "id": "123",
  "name": "Producto de ejemplo",
  "description": "Descripción del producto",
  "price": 99.99,
  "stock": 10,
  "variants": [
    {
      "size": "L",
      "color": "Rojo",
      "price": 99.99,
      "stock": 5,
      "imageUrl": "http://ejemplo.com/imagen.jpg"
    }
  ]
}
```

### Listar productos
- **Método**: GET
- **Ruta**: `/api/products?page=1&limit=10`
- **Respuesta**: 200 OK
```json
{
  "products": [
    {
      "id": "123",
      "name": "Producto de ejemplo",
      "description": "Descripción del producto",
      "price": 99.99,
      "stock": 10,
      "variants": [
        {
          "size": "L",
          "color": "Rojo",
          "price": 99.99
        }
      ]
    }
  ],
  "page": 1,
  "limit": 10,
  "total": 1,
  "totalPages": 1
}
```

### Actualizar un producto
- **Método**: PUT
- **Ruta**: `/api/products/{id}`
- **Body**:
```json
{
  "name": "Nombre actualizado",
  "price": 129.99,
  "stock": 15
}
```
- **Respuesta**: 200 OK
```json
{
  "status": "ok"
}
```

### Eliminar un producto
- **Método**: DELETE
- **Ruta**: `/api/products/{id}`
- **Respuesta**: 204 No Content

## Gestión de Inventario

### Ajustar stock
- **Método**: POST
- **Ruta**: `/api/inventory/adjust/{id}`
- **Body**:
```json
{
  "quantity": 5,
  "reason": "Reposición de stock"
}
```
- **Respuesta**: 200 OK
```json
{
  "status": "ok",
  "message": "Stock ajustado correctamente"
}
```

### Buscar productos
- **Método**: GET
- **Ruta**: `/api/inventory/search?name=ejemplo&minPrice=50&maxPrice=200&minStock=5`
- **Respuesta**: 200 OK
```json
{
  "total": 1,
  "products": [
    {
      "id": "123",
      "name": "Producto de ejemplo",
      "description": "Descripción del producto",
      "price": 99.99,
      "stock": 10,
      "variants": 1
    }
  ]
}
```

### Productos con stock bajo
- **Método**: GET
- **Ruta**: `/api/inventory/low-stock?threshold=5`
- **Respuesta**: 200 OK
```json
{
  "total": 1,
  "threshold": 5,
  "products": [
    {
      "id": "123",
      "name": "Producto de ejemplo",
      "stock": 3,
      "price": 99.99
    }
  ]
}
```

## Estadísticas

### Estadísticas generales
- **Método**: GET
- **Ruta**: `/api/stats/products`
- **Respuesta**: 200 OK
```json
{
  "totalProducts": 10,
  "totalStock": 150,
  "totalValue": 15000,
  "lowStockCount": 3,
  "categories": {
    "Categoría General": 10
  }
}
```

### Valor de inventario
- **Método**: GET
- **Ruta**: `/api/stats/inventory-value`
- **Respuesta**: 200 OK
```json
{
  "totalProducts": 10,
  "products": [
    {
      "id": "123",
      "name": "Producto de ejemplo",
      "stock": 10,
      "price": 99.99,
      "value": 999.90
    }
  ]
}
```
