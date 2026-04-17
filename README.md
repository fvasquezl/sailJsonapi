# sailJsonapi

Proyecto de aprendizaje: API REST siguiendo la especificación [JSON:API](https://jsonapi.org/) con Laravel 13 y Sanctum.

## Stack

| Paquete | Versión |
|---------|---------|
| PHP | 8.5 |
| Laravel | 13 |
| laravel-json-api/laravel | 5.1 |
| Laravel Sanctum | 4 |
| Pest | 4 |
| Laravel Sail | 1 |

## Levantar el proyecto

```bash
# Instalar dependencias
vendor/bin/sail composer install

# Copiar variables de entorno
cp .env.example .env

# Generar clave
vendor/bin/sail artisan key:generate

# Ejecutar migraciones con seeders
vendor/bin/sail artisan migrate --seed

# Levantar contenedores
vendor/bin/sail up -d
```

## Correr tests

```bash
# Todos los tests
vendor/bin/sail artisan test --compact

# Un archivo específico
vendor/bin/sail artisan test --compact tests/Feature/Auth/RegisterTest.php

# Filtrar por nombre
vendor/bin/sail artisan test --compact --filter="can register"
```

## Endpoints de autenticación

| Método | Ruta | Middleware | Descripción |
|--------|------|------------|-------------|
| `POST` | `/api/v1/register` | `guest:sanctum` | Registrar nuevo usuario |
| `POST` | `/api/v1/login` | — | Iniciar sesión |
| `POST` | `/api/v1/logout` | `auth:sanctum` | Cerrar sesión |
| `GET` | `/api/v1/user` | `auth:sanctum` | Usuario autenticado |

### Registro

```json
POST /api/v1/register
{
    "name": "Faustino Vasquez",
    "email": "fvasquez@example.com",
    "password": "password",
    "password_confirmation": "password",
    "device_name": "Mi dispositivo"
}
```

Respuesta: token Sanctum en `plain-text-token`.

Si el usuario ya está autenticado, devuelve `204 No Content`.

### Login

```json
POST /api/v1/login
{
    "email": "fvasquez@example.com",
    "password": "password",
    "device_name": "Mi dispositivo"
}
```

Si ya hay sesión activa, devuelve `204 No Content`.

### Logout

```
POST /api/v1/logout
Authorization: Bearer {token}
```

Elimina el token actual. Devuelve `204 No Content`.

## Recursos JSON:API

Base URL: `/api/v1`

### Articles

| Método | Ruta | Auth requerida | Descripción |
|--------|------|----------------|-------------|
| `GET` | `/articles` | No | Listar artículos |
| `GET` | `/articles/{slug}` | No | Ver artículo |
| `POST` | `/articles` | Sí | Crear artículo |
| `PATCH` | `/articles/{slug}` | Sí (dueño) | Actualizar artículo |
| `DELETE` | `/articles/{slug}` | Sí (dueño) | Eliminar artículo |

**Relaciones:**
- `GET /articles/{slug}/authors` — autor del artículo
- `GET /articles/{slug}/categories` — categoría del artículo

**Filtros disponibles:** `title`, `content`, `year`, `month`, `search`, `categories`

**Ordenamiento:** `title`, `content`, `created_at`

**Includes:** `?include=authors`, `?include=categories`

### Authors

| Método | Ruta | Descripción |
|--------|------|-------------|
| `GET` | `/authors` | Listar autores |
| `GET` | `/authors/{id}` | Ver autor |
| `GET` | `/authors/{id}/articles` | Artículos del autor |

### Categories

| Método | Ruta | Auth requerida | Descripción |
|--------|------|----------------|-------------|
| `GET` | `/categories` | No | Listar categorías |
| `GET` | `/categories/{slug}` | No | Ver categoría |
| `POST` | `/categories` | Sí | Crear categoría |
| `PATCH` | `/categories/{slug}` | Sí (dueño) | Actualizar categoría |
| `DELETE` | `/categories/{slug}` | Sí (dueño) | Eliminar categoría |

## Modelos y relaciones

```
User ──< Article >── Category
```

- `User` tiene muchos `Article`
- `Article` pertenece a `User` (autor) y a `Category`
- `Category` tiene muchos `Article`
- `Article` usa `slug` como route key

## Autorización

La autorización se implementa con `Authorizer` + `Policy` por recurso:

- **Articles**: `ArticleAuthorizer` + `ArticlePolicy` — solo el dueño puede actualizar/eliminar.
- **Categories**: `CategoryAuthorizer` — solo el dueño puede actualizar/eliminar.

Las acciones públicas (`index`, `show`) no requieren autenticación.

## Estructura relevante

```
app/
  Http/
    Controllers/Api/
      RegisterController.php
      LoginController.php
      UserController.php
      V1/ArticleController.php
    Middleware/
      RedirectIfAuthenticated.php   # devuelve 204 para JSON cuando ya hay sesión
    Responses/
      TokenResponse.php
  JsonApi/V1/
    Articles/
      ArticleSchema.php
      ArticleResource.php
      ArticleRequest.php
      ArticleQuery.php
      ArticleAuthorizer.php
    Authors/
      AuthorSchema.php
      AuthorResource.php
      AuthorQuery.php
    Categories/
      CategorySchema.php
      CategoryResource.php
      CategoryRequest.php
      CategoryAuthorizer.php
    Server.php
  Models/
    User.php
    Article.php
    Category.php
  Policies/
    ArticlePolicy.php

tests/Feature/
  Auth/         # Register, Login, Logout, AuthenticatedUser
  Articles/     # List, Create, Update, Delete, Sort, Paginate, Filter, Include
  Authors/      # List, IncludeArticles
  Categories/   # List, Create, Update, Delete, IncludeArticles
```

## Ramas y checkpoints

| Rama / Tag | Descripción |
|------------|-------------|
| `main` | Rama principal |
| `v1.0-sanctum-auth` | Checkpoint: auth con Sanctum completa (registro, login, logout, usuario) |
| `feature/simple-permissions` | Trabajo en progreso: sistema de permisos simples |

Para volver al checkpoint de auth:
```bash
git checkout v1.0-sanctum-auth
```
