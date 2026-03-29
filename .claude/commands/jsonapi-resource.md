---
description: Genera un recurso JSON:API completo (schema, resource, requests, controller) y lo registra correctamente
argument-hint: <tipo> <Modelo>  (ej: categories Category)
---

Genera un recurso JSON:API completo para el tipo `$ARGUMENTS`.

El argumento tiene el formato `<tipo> <Modelo>` — por ejemplo `categories Category`.

Sigue estos pasos en orden:

## 1. Generar las clases

```bash
vendor/bin/sail artisan jsonapi:schema {tipo} --model={Modelo} --server=v1 --no-interaction
vendor/bin/sail artisan jsonapi:resource {tipo} --model={Modelo} --server=v1 --no-interaction
vendor/bin/sail artisan jsonapi:requests {tipo} --server=v1 --no-interaction
vendor/bin/sail artisan jsonapi:controller Api/V1/{Modelo}Controller --no-interaction
```

## 2. Registrar el schema en Server.php

Abre `app/JsonApi/V1/Server.php` y agrega el nuevo schema en `allSchemas()`:

```php
protected function allSchemas(): array {
    return [
        Articles\ArticleSchema::class,
        {Namespace}\{Modelo}Schema::class,  // agregar aquí
    ];
}
```

**Sin este paso:** el paquete responde 400 "Resource type X is not recognised." en cualquier request que use este tipo como relación.

## 3. Verificar el Schema generado

En `app/JsonApi/V1/{Pluralizado}/{Modelo}Schema.php`:
- Revisar `fields()`: confirmar que los campos del modelo están declarados con los tipos correctos (`Str`, `DateTime`, `ID`, etc.)
- Si tiene relaciones con otros recursos, agregar `BelongsTo::make()` o `HasMany::make()` según corresponda

## 4. Verificar el Request generado

En `app/JsonApi/V1/{Pluralizado}/{Modelo}Request.php`, agregar reglas para **todos** los campos y relaciones necesarios:

```php
public function rules(): array {
    return [
        'name'     => ['required', 'string'],
        // si tiene BelongsTo hacia otro recurso:
        'category' => ['required'],
    ];
}
```

**Sin reglas para las relaciones:** el hydrator las ignora y el INSERT falla con NOT NULL constraint en la FK.

## 5. Registrar la ruta

En `routes/api.php`, agregar el recurso:

```php
JsonApiRoute::server('v1')->name('api.v1.')->resources(function ($server) {
    $server->resource('articles', ArticleController::class);
    $server->resource('{tipo}', {Modelo}Controller::class);  // agregar aquí
});
```

## 6. Exponer relaciones en el Resource (si aplica)

En `app/JsonApi/V1/{Pluralizado}/{Modelo}Resource.php`, si el modelo tiene relaciones a exponer:

```php
public function relationships($request): iterable {
    return [
        $this->relation('category'),
        $this->relation('user'),
    ];
}
```

## 7. Correr tests y formatear

```bash
vendor/bin/sail artisan test --compact
vendor/bin/sail bin pint --dirty --format agent
```
