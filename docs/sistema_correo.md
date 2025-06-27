# Sistema de Correo para Gestión de Inventario

Este documento explica cómo está implementado el sistema de correo electrónico dentro de la aplicación de Gestión de Inventario, siguiendo los principios de Arquitectura Hexagonal y Domain-Driven Design (DDD).

## Requisitos

Para que el sistema de correo funcione correctamente, se requieren los siguientes paquetes:

```bash
# Componente Mailer de Symfony
composer require symfony/mailer

# Componente Twig (necesario para las plantillas de correo y las vistas web)
composer require symfony/twig-bundle
```

El proyecto actualmente utiliza las siguientes versiones:

- symfony/twig-bridge: 7.0.8
- symfony/twig-bundle: 7.0.8
- twig/twig: 3.21.1

## Estructura

El sistema de correo está implementado siguiendo la arquitectura hexagonal:

- **Puerto (Interfaz)**: `App\Infrastructure\Email\Contract\EmailSenderInterface`
- **Adaptador**: `App\Infrastructure\Email\SymfonyMailer`

## Configuración

### Configuración del Servicio

El servicio de correo está configurado en `config/services.yaml`:

```yaml
parameters:
    app.mailer.from_email: 'noreply@inventario.example.com'
    app.mailer.from_name: 'Sistema de Gestión de Inventario'

services:
    # ...
    
    # Configuración del servicio de email
    App\Infrastructure\Email\Contract\EmailSenderInterface:
        class: App\Infrastructure\Email\SymfonyMailer
        arguments:
            $fromEmail: '%app.mailer.from_email%'
            $fromName: '%app.mailer.from_name%'
```

### Configuración del DSN

El DSN (Data Source Name) del mailer se configura en el archivo `.env`:

```properties
###> symfony/mailer ###
# Para desarrollo, usa smtp o un servicio de captación de correos como Mailtrap
# MAILER_DSN=smtp://user:pass@smtp.example.com:25
# Para guardar correos como archivos (requiere instalar symfony/native-mailer)
# MAILER_DSN=native://default?dsn=file://%kernel.project_dir%/var/email
# Para usar un servidor SMTP local
# MAILER_DSN=smtp://localhost:1025
# Para deshabilitar el envío de correos pero simular el proceso (recomendado para desarrollo)
MAILER_DSN=null://null
###< symfony/mailer ###
```

### Guardado de correos en desarrollo

Para facilitar las pruebas en desarrollo, se ha implementado una característica que guarda todos los correos electrónicos como archivos en el directorio `var/email`, independientemente del transporte configurado. Esto permite visualizar los correos enviados a través de la interfaz web en `/email-test`.

Esta funcionalidad está implementada en la clase `SymfonyMailer` y solo está activa en el entorno de desarrollo (`APP_ENV=dev`).

### Rutas para Pruebas de Correo

La aplicación incluye las siguientes rutas para probar el sistema de correo:

1. **`/email-test`**: Muestra todos los correos guardados en `var/email`.
2. **`/email-test/send`**: Envía un correo de prueba y redirige a `/email-test`.
3. **`/email-test/clear`**: Elimina todos los correos guardados y redirige a `/email-test`.
4. **`/simple-email-test`**: Muestra información sobre la configuración del correo y permite enviar un correo de prueba.

También puedes enviar un correo de prueba a través de la línea de comandos:

```bash
php bin/console app:send-test-email correo@destino.com
```

Para el desarrollo, utilizamos MailHog, un servidor SMTP falso que captura todos los correos enviados por la aplicación y los muestra en una interfaz web.

### Iniciar MailHog

MailHog está configurado en el archivo `compose.yaml`. Para iniciar MailHog:

```bash
docker-compose up -d mailhog
```

### Acceder a la Interfaz Web de MailHog

Una vez iniciado, puedes acceder a la interfaz web de MailHog en:

```
http://localhost:8025
```

Aquí podrás ver todos los correos enviados por la aplicación.

## Uso en Producción

Para producción, debes configurar un servidor SMTP real:

1. Edita el archivo `.env.local` (o `.env` si estás en producción):

```properties
MAILER_DSN=smtp://usuario:contraseña@smtp.tudominio.com:587
```

2. También puedes usar otros transportes soportados por Symfony Mailer como Amazon SES, Mailgun, SendGrid, etc.:

```properties
# SendGrid
MAILER_DSN=sendgrid://KEY@default

# Mailgun
MAILER_DSN=mailgun://KEY:DOMAIN@default

# Amazon SES
MAILER_DSN=ses://ACCESS_KEY:SECRET_KEY@default
```

## Eventos que Envían Correos

Los siguientes eventos de dominio disparan el envío de correos:

1. **ProductCreatedEvent**: Cuando se crea un nuevo producto.
2. **ProductUpdatedEvent**: Cuando se actualiza un producto existente.
3. **ProductDeletedEvent**: Cuando se elimina un producto.

## Personalización de Plantillas

Actualmente, las plantillas de correo están definidas directamente en los listeners. Para una solución más escalable, considera:

1. Crear plantillas Twig.
2. Mover las plantillas a archivos HTML separados.
3. Implementar un sistema de plantillas personalizado.

## Solución de Problemas

Si los correos no se envían:

1. Verifica que el servicio de correo esté correctamente configurado en `services.yaml`.
2. Asegúrate de que el DSN en `.env` o `.env.local` sea correcto.
3. Comprueba que MailHog (o tu servidor SMTP) esté en funcionamiento.
4. Revisa los logs de la aplicación para ver posibles errores.

Si las rutas relacionadas con el correo no funcionan:

1. Verifica que las rutas estén correctamente configuradas en `config/routes.yaml`.
2. Asegúrate de que el namespace de tus controladores sea correcto.
3. Limpia la caché de Symfony después de realizar cambios: `php bin/console cache:clear`.

### Configuración de Rutas

Las rutas de los controladores en `src/UI/Controller` deben estar configuradas en `config/routes.yaml`:

```yaml
controllers:
    resource:
        path: ../src/Controller/
        namespace: App\Controller
    type: attribute

ui_controllers:
    resource:
        path: ../src/UI/Controller/
        namespace: App\UI\Controller
    type: attribute
```

## Extensión

Para añadir nuevos tipos de notificaciones por correo:

1. Inyecta `EmailSenderInterface` en tu servicio o listener.
2. Usa el método `send()` para enviar correos.

Ejemplo:

```php
use App\Infrastructure\Email\Contract\EmailSenderInterface;

class MiServicio
{
    private EmailSenderInterface $emailSender;
    
    public function __construct(EmailSenderInterface $emailSender)
    {
        $this->emailSender = $emailSender;
    }
    
    public function miMetodo(): void
    {
        // ... lógica del método
        
        $this->emailSender->send(
            'destinatario@example.com',
            'Asunto del Correo',
            '<h1>Contenido HTML</h1><p>Este es un correo de prueba.</p>'
        );
    }
}
```
