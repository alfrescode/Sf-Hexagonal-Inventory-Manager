# Sistema de Gestión de Inventario - DDD & Arquitectura Hexagonal

## Estructura Completa del Proyecto

```
src/
├── Application/                    # Capa de Aplicación
│   ├── Command/                   # Comandos (escritura)
│   │   ├── CreateProduct/
│   │   │   ├── CreateProductCommand.php
│   │   │   └── CreateProductHandler.php
│   │   └── UpdateProduct/
│   │       ├── UpdateProductCommand.php
│   │       └── UpdateProductHandler.php
│   ├── Query/                     # Consultas (lectura)
│   │   ├── GetProduct/
│   │   │   ├── GetProductQuery.php
│   │   │   └── GetProductHandler.php
│   │   └── ListProducts/
│   │       ├── ListProductsQuery.php
│   │       └── ListProductsHandler.php
│   └── Event/                     # Eventos de Aplicación
│       └── ProductCreatedListener.php
│
├── Domain/                        # Capa de Dominio
│   ├── Product/                   # Agregado Product
│   │   ├── Contract/             # Interfaces/Contratos
│   │   │   ├── ProductRepositoryInterface.php
│   │   │   └── ProductFactoryInterface.php
│   │   ├── Event/                # Eventos de Dominio
│   │   │   ├── ProductCreatedEvent.php
│   │   │   └── ProductUpdatedEvent.php
│   │   ├── Exception/            # Excepciones de Dominio
│   │   │   ├── InvalidProductException.php
│   │   │   └── ProductNotFoundException.php
│   │   ├── Service/              # Servicios de Dominio
│   │   │   └── ProductFactory.php
│   │   ├── ValueObject/          # Objetos de Valor
│   │   │   ├── ProductId.php
│   │   │   ├── ProductName.php
│   │   │   ├── ProductPrice.php
│   │   │   └── ProductStock.php
│   │   ├── Product.php           # Entidad Raíz
│   │   └── ProductVariant.php    # Entidad
│   │
│   └── Service/                  # Servicios de Dominio Compartidos
│       └── EventDispatcher.php
│
├── Infrastructure/               # Capa de Infraestructura
│   ├── Persistence/             # Implementaciones de Persistencia
│   │   ├── Doctrine/
│   │   │   ├── Entity/
│   │   │   │   └── ProductEntity.php
│   │   │   ├── Repository/
│   │   │   │   └── DoctrineProductRepository.php
│   │   │   └── Mapping/
│   │   │       └── Product.orm.xml
│   │   └── File/
│   │       └── FileProductRepository.php
│   ├── Email/                   # Servicios de Email
│   │   ├── Contract/
│   │   │   └── EmailSenderInterface.php
│   │   ├── SmtpMailer.php
│   │   ├── SesMailer.php
│   │   └── SendGridMailer.php
│   └── Service/                 # Servicios de Infraestructura
│       └── SymfonyEventDispatcher.php
│
├── Shared/                      # Código Compartido
│   └── Domain/                  # Conceptos de Dominio Compartidos
│       ├── Aggregate/
│       │   └── AggregateRoot.php
│       ├── Bus/
│       │   ├── Command/
│       │   │   └── CommandBus.php
│       │   └── Query/
│       │       └── QueryBus.php
│       ├── Event/
│       │   ├── DomainEvent.php
│       │   └── EventDispatcher.php
│       └── ValueObject/         # Value Objects Base
│           ├── Uuid.php
│           └── Money.php
│
└── UI/                          # Interfaces de Usuario
    ├── Rest/                    # API REST
    │   ├── Controller/
    │   │   └── ProductController.php
    │   └── DTO/                # Objetos de Transferencia de Datos
    │       ├── CreateProductRequest.php
    │       └── ProductResponse.php
    └── CLI/                     # Interfaz de Línea de Comandos
        └── Command/
            └── CreateProductCommand.php

tests/                          # Tests
├── Application/               # Tests de Aplicación
│   ├── Command/
│   │   └── CreateProductHandlerTest.php
│   └── Query/
│       └── GetProductHandlerTest.php
├── Domain/                    # Tests de Dominio
│   └── Product/
│       ├── ProductTest.php
│       └── ValueObject/
│           └── ProductIdTest.php
├── Infrastructure/            # Tests de Infraestructura
│   ├── Persistence/
│   │   └── DoctrineProductRepositoryTest.php
│   └── Email/
│       └── SmtpMailerTest.php
└── UI/                        # Tests de UI
    ├── Rest/
    │   └── Controller/
    │       └── ProductControllerTest.php
    └── CLI/
        └── Command/
            └── CreateProductCommandTest.php
```

Esta estructura completa refleja:

1. **Separación Clara de Capas**
   - Domain: Núcleo de la aplicación
   - Application: Casos de uso
   - Infrastructure: Implementaciones técnicas
   - UI: Interfaces de usuario
   - Shared: Código común

2. **Implementación CQRS**
   - Commands: Modificación de estado
   - Queries: Consultas de datos
   - Separación clara de responsabilidades

3. **Patrones DDD**
   - Aggregates
   - Value Objects
   - Domain Events
   - Repositories
   - Factories

4. **Testing Completo**
   - Tests por capa
   - Cobertura completa
   - Estructura espejo del código fuente

5. **Principios SOLID**
   - Interfaces claras
   - Dependencias invertidas
   - Clases con responsabilidad única

6. **Infraestructura Flexible**
   - Múltiples implementaciones de persistencia
   - Varios servicios de email
   - Fácilmente extensible

## Estructura del Proyecto

```
src/
├── Domain/                           # Capa de Dominio: Entidades, Value Objects, Eventos
│   └── Product/
│       ├── ValueObject/             # Value Objects del dominio Product
│       │   ├── ProductId.php
│       │   └── ProductPrice.php
│       ├── Event/                   # Eventos del dominio
│       │   └── ProductCreated.php
│       ├── Service/                 # Servicios del dominio
│       │   └── ProductFactory.php
│       ├── Product.php             # Entidad agregada raíz
│       ├── ProductVariant.php      # Entidad para variantes
│       └── ProductRepository.php   # Interface del repositorio (Puerto)
│   └── Product/
│       ├── Product.php              # Entidad agregada raíz
│       ├── ProductId.php            # Value Object para el ID
│       ├── ProductVariant.php       # Entidad para variantes
│       ├── ProductRepositoryInterface.php  # Puerto de repositorio
│       ├── Event/
│       │   └── ProductCreated.php   # Evento de dominio
│       └── Service/
│           └── ProductFactory.php    # Factoría de productos
├── Application/                      # Capa de Aplicación
│   └── Command/                     # Implementación CQRS - Comandos
│       ├── CreateProductCommand.php
│       ├── CreateProductHandler.php
│   └── Event/
│       └── ProductCreatedListener.php # Manejador de eventos
├── Infrastructure/                    # Capa de Infraestructura
│   ├── Persistence/                  # Adaptadores de persistencia
│   │   ├── DoctrineProductRepository.php  # Implementación MySQL
│   │   └── FileProductRepository.php      # Implementación alternativa
│   └── Email/                        # Adaptadores de email
│       ├── EmailNotifier.php         # Interface del notificador
│       ├── SmtpMailer.php           # Implementación SMTP
│       ├── SesMailer.php            # Implementación Amazon SES
│       └── SendGridMailer.php        # Implementación SendGrid
│   ├── Shared/                      # Código compartido entre capas
│   └── Domain/
│       └── ValueObject/            # Value Objects comunes
│           ├── Uuid.php
│           └── Money.php
└── UI/                            # Interfaces de Usuario
    ├── Rest/                      # API REST
    │   └── Controller/
    │       └── ProductController.php
    └── CLI/                       # Interfaz de Línea de Comandos
        └── Command/
            └── CreateProductCommand.php
```

## Estrategia de Implementación

### 1. Dominio (Domain Layer)
- **Product**: Entidad agregada raíz que contiene la lógica de negocio principal
- **ProductVariant**: Entidad que representa las variaciones de producto
- **ProductId**: Value Object para garantizar la integridad del identificador
- **ProductRepositoryInterface**: Puerto que define las operaciones de persistencia
- **ProductCreated**: Evento de dominio para notificar la creación de productos
- **ProductFactory**: Servicio de dominio para la creación de productos

### 2. Aplicación (Application Layer)
- Implementación de CQRS:
  - Commands: Representan las intenciones de modificar el estado
  - Handlers: Ejecutan la lógica de aplicación
- Event Listeners: Reaccionan a eventos del dominio

### 3. Infraestructura (Infrastructure Layer)
- **Persistencia**: 
  - DoctrineProductRepository: Implementación MySQL
  - FileProductRepository: Implementación alternativa
- **Email**:
  - Diferentes implementaciones de envío de correo (SMTP, SES, SendGrid)
- **API**:
  - REST endpoints para la gestión de productos

## Principios SOLID Aplicados

1. **Single Responsibility (SRP)**:
   - Cada clase tiene una única responsabilidad
   - Separación clara entre comandos y consultas (CQRS)

2. **Open/Closed (OCP)**:
   - Las interfaces permiten extensiones sin modificar el código existente
   - Nuevos adaptadores de email pueden agregarse sin cambiar el dominio

3. **Liskov Substitution (LSP)**:
   - Todas las implementaciones de repositorio son intercambiables
   - Los servicios de email son sustituibles entre sí

## Flujo de Trabajo

1. La API REST recibe peticiones JSON
2. Los comandos se crean y se pasan a sus handlers
3. Los handlers utilizan el dominio para realizar operaciones
4. El dominio emite eventos cuando ocurren cambios importantes
5. Los listeners reaccionan a los eventos (ej: envío de emails)
6. La persistencia se realiza a través de los puertos del dominio
