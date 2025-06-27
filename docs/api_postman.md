# Guía para usar la API con Postman

Esta guía te mostrará cómo interactuar con la API de gestión de inventario utilizando Postman.

## Crear un producto

- **Método**: POST
- **URL**: `/api/products`
- **Cuerpo** (JSON):

```json
{
    "name": "Producto de Ejemplo",
    "description": "Este es un producto de ejemplo creado con Postman",
    "price": 99.99,
    "stock": 50,
    "variants": [
        {
            "size": "M",
            "color": "Rojo",
            "price": 99.99,
            "stock": 20,
            "imageUrl": "https://example.com/imagen-rojo.jpg"
        },
        {
            "size": "L",
            "color": "Azul",
            "price": 109.99,
            "stock": 15,
            "imageUrl": "https://example.com/imagen-azul.jpg"
        }
    ]
}
```

## Listar todos los productos

- **Método**: GET
- **URL**: `/api/products`

## Obtener un producto específico

- **Método**: GET
- **URL**: `/api/products/{id}`

## Actualizar un producto

- **Método**: PUT
- **URL**: `/api/products/{id}`
- **Cuerpo** (JSON):

```json
{
    "name": "Producto Actualizado",
    "description": "Este producto ha sido actualizado",
    "price": 129.99,
    "stock": 75,
    "variants": [
        {
            "size": "M",
            "color": "Verde",
            "price": 129.99,
            "stock": 25,
            "imageUrl": "https://example.com/imagen-verde.jpg"
        }
    ]
}
```

## Eliminar un producto

- **Método**: DELETE
- **URL**: `/api/products/{id}`

## Ajustar el inventario de un producto

- **Método**: POST
- **URL**: `/api/inventory/adjust/{id}`
- **Cuerpo** (JSON):

```json
{
    "quantity": 10,
    "operation": "add"
}
```

Opciones para `operation`: `add` (añadir), `subtract` (restar), `set` (establecer)

## Buscar productos

- **Método**: GET
- **URL**: `/api/inventory/search?query=termino`

## Obtener productos con bajo stock

- **Método**: GET
- **URL**: `/api/inventory/low-stock?threshold=10`

## Estadísticas de productos

- **Método**: GET
- **URL**: `/api/stats/products`

## Valor total del inventario

- **Método**: GET
- **URL**: `/api/stats/inventory-value`
