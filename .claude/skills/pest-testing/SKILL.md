---
name: pest-testing
description: "Use this skill for Pest PHP testing in Laravel projects only. Trigger whenever any test is being written, edited, fixed, or refactored — including fixing tests that broke after a code change, adding assertions, converting PHPUnit to Pest, adding datasets, and TDD workflows. Always activate when the user asks how to write something in Pest, mentions test files or directories (tests/Feature, tests/Unit, tests/Browser), or needs browser testing, smoke testing multiple pages for JS errors, or architecture tests. Covers: it()/expect() syntax, datasets, mocking, browser testing (visit/click/fill), smoke testing, arch(), Livewire component tests, RefreshDatabase, and all Pest 4 features. Do not use for factories, seeders, migrations, controllers, models, or non-test PHP code."
license: MIT
metadata:
  author: laravel
---

# Pest Testing 4

## Documentation

Use `search-docs` for detailed Pest 4 patterns and documentation.

## Basic Usage

### Creating Tests

All tests must be written using Pest. Use `vendor/bin/sail artisan make:test --pest {name}`.

### Test Organization

- Unit/Feature tests: `tests/Feature` and `tests/Unit` directories.
- Browser tests: `tests/Browser/` directory.
- Do NOT remove tests without approval - these are core application code.

### Basic Test Structure

<!-- Basic Pest Test Example -->
```php
it('is true', function () {
    expect(true)->toBeTrue();
});
```

### Running Tests

- Run minimal tests with filter before finalizing: `vendor/bin/sail artisan test --compact --filter=testName`.
- Run all tests: `vendor/bin/sail artisan test --compact`.
- Run file: `vendor/bin/sail artisan test --compact tests/Feature/ExampleTest.php`.

## Assertions

Use specific assertions (`assertSuccessful()`, `assertNotFound()`) instead of `assertStatus()`:

<!-- Pest Response Assertion -->
```php
it('returns all', function () {
    $this->postJson('/api/docs', [])->assertSuccessful();
});
```

| Use | Instead of |
|-----|------------|
| `assertSuccessful()` | `assertStatus(200)` |
| `assertNotFound()` | `assertStatus(404)` |
| `assertForbidden()` | `assertStatus(403)` |

## Mocking

Import mock function before use: `use function Pest\Laravel\mock;`

## Datasets

Use datasets for repetitive tests (validation rules, etc.):

<!-- Pest Dataset Example -->
```php
it('has emails', function (string $email) {
    expect($email)->not->toBeEmpty();
})->with([
    'james' => 'james@laravel.com',
    'taylor' => 'taylor@laravel.com',
]);
```

## Pest 4 Features

| Feature | Purpose |
|---------|---------|
| Browser Testing | Full integration tests in real browsers |
| Smoke Testing | Validate multiple pages quickly |
| Visual Regression | Compare screenshots for visual changes |
| Test Sharding | Parallel CI runs |
| Architecture Testing | Enforce code conventions |

### Browser Test Example

Browser tests run in real browsers for full integration testing:

- Browser tests live in `tests/Browser/`.
- Use Laravel features like `Event::fake()`, `assertAuthenticated()`, and model factories.
- Use `RefreshDatabase` for clean state per test.
- Interact with page: click, type, scroll, select, submit, drag-and-drop, touch gestures.
- Test on multiple browsers (Chrome, Firefox, Safari) if requested.
- Test on different devices/viewports (iPhone 14 Pro, tablets) if requested.
- Switch color schemes (light/dark mode) when appropriate.
- Take screenshots or pause tests for debugging.

<!-- Pest Browser Test Example -->
```php
it('may reset the password', function () {
    Notification::fake();

    $this->actingAs(User::factory()->create());

    $page = visit('/sign-in');

    $page->assertSee('Sign In')
        ->assertNoJavaScriptErrors()
        ->click('Forgot Password?')
        ->fill('email', 'nuno@laravel.com')
        ->click('Send Reset Link')
        ->assertSee('We have emailed your password reset link!');

    Notification::assertSent(ResetPassword::class);
});
```

### Smoke Testing

Quickly validate multiple pages have no JavaScript errors:

<!-- Pest Smoke Testing Example -->
```php
$pages = visit(['/', '/about', '/contact']);

$pages->assertNoJavaScriptErrors()->assertNoConsoleLogs();
```

### Visual Regression Testing

Capture and compare screenshots to detect visual changes.

### Test Sharding

Split tests across parallel processes for faster CI runs.

### Architecture Testing

Pest 4 includes architecture testing (from Pest 3):

<!-- Architecture Test Example -->
```php
arch('controllers')
    ->expect('App\Http\Controllers')
    ->toExtendNothing()
    ->toHaveSuffix('Controller');
```

## Common Pitfalls

- Not importing `use function Pest\Laravel\mock;` before using mock
- Using `assertStatus(200)` instead of `assertSuccessful()`
- Forgetting datasets for repetitive validation tests
- Deleting tests without approval
- Forgetting `assertNoJavaScriptErrors()` in browser tests

## Project-specific patterns (Sail/JSON:API)

### JSON:API requests — use `$this->jsonApi()`, not `getJson()/postJson()`

This project uses `laravel-json-api/laravel`. The TestCase already includes `MakesJsonApiRequests`. Always use `$this->jsonApi()` for JSON:API endpoints — it sends `Accept: application/vnd.api+json`. Plain `getJson()`/`postJson()` send `application/json` and the package responds **406 Not Acceptable**.

```php
$this->jsonApi()
    ->withData($payload)
    ->post(route('api.v1.articles.store'))
    ->assertCreated();
```

For non-JSON:API endpoints (login, register, logout, user) — use `postJson()`/`getJson()` as normal Laravel.

### Dataset keys MUST be descriptive (not numeric)

```php
// GOOD — runner shows: "rejects invalid slugs with data set 'empty'"
->with([
    'empty' => ['', null],
    'contains underscores' => ['with_underscores', 'validation.no_underscores'],
]);

// BAD — runner shows: "rejects invalid slugs with data set #0"
->with([
    ['', null],
    ['with_underscores', 'validation.no_underscores'],
]);
```

### Don't write `/** @var TestCase $this */`

`tests/Pest.php` already binds with `pest()->extend(TestCase::class)->in('Feature', 'Unit')`. PHPStorm/PHPStan resolve `$this` automatically. The annotation is noise.

### Status helpers — prefer specific over `assertStatus()`

| Use | Instead of |
|---|---|
| `assertOk()` | `assertStatus(200)`, `assertOK()` (uppercase works but isn't canonical) |
| `assertCreated()` | `assertStatus(201)` |
| `assertNoContent()` | `assertStatus(204)` |
| `assertUnauthorized()` | `assertStatus(401)` |
| `assertForbidden()` | `assertStatus(403)` |
| `assertNotFound()` | `assertStatus(404)` |
| `assertUnprocessable()` | `assertStatus(422)` |

`assertStatus(400)` has no native helper — leave as-is.

### `assertDatabaseHas/Missing` with FK columns — use `->id`, not `getRouteKey()`

If a model uses a non-id route key (e.g. `Category::getRouteKeyName(): 'slug'`), then `$category->getRouteKey()` returns the slug string. Comparing it against an integer FK column never matches → `assertDatabaseMissing` silently passes without validating anything.

```php
// BAD — silently no-op
$this->assertDatabaseHas('articles', [
    'category_id' => $category->getRouteKey(),  // string slug
]);

// GOOD
$this->assertDatabaseHas('articles', [
    'id' => $article->id,
    'category_id' => $category->id,
]);
```

In JSON:API payloads (`withData()`), `getRouteKey()` is correct because the spec identifies resources by route key.

### Don't use `withoutExceptionHandling()` for 401/403 tests

It propagates `AuthenticationException`/`AuthorizationException` raw instead of letting them become HTTP responses — the test fails on uncaught exception instead of getting `assertUnauthorized()/assertForbidden()`.

To debug other failures:
- `$response->dump()` / `$response->dd()` — see body as-is
- `Exceptions::fake()` (Laravel 11+) — inspect exceptions without breaking flow
- `$this->withoutExceptionHandling([SpecificException::class])` — only one type propagates

### Skip tests intentionally with deduped setup

When tests share setup but a few diverge, `beforeEach()` in the file applies to all. Don't force-fit divergent tests into shared state — keep them inline.

### When NOT to merge tests into a dataset

- Setup creates different prerequisite data (e.g., one test needs an existing record, another doesn't)
- Final assertions differ (`assertDatabaseCount` vs `assertDatabaseMissing`)
- Different mocking, auth scopes, or middleware

Forcing these into a dataset requires conditional flags inside the test, which negates the readability win.

### Collapse repeated index assertions with `collect()->map()`

For JSON:API index endpoints, don't hand-roll one entry per resource. Map the factory collection into the expected `data` array:

```php
// BAD — 50+ near-identical lines
$this->jsonApi()->get(route('api.v1.articles.index'))
    ->assertJson([
        'data' => [
            ['type' => 'articles', 'id' => $articles[0]->id, 'attributes' => [...]],
            ['type' => 'articles', 'id' => $articles[1]->id, 'attributes' => [...]],
            ['type' => 'articles', 'id' => $articles[2]->id, 'attributes' => [...]],
        ],
    ]);

// GOOD
$this->jsonApi()->get(route('api.v1.articles.index'))
    ->assertJson([
        'data' => $articles->map(fn ($article) => [
            'type' => 'articles',
            'id' => (string) $article->getRouteKey(),
            'attributes' => [
                'title' => $article->title,
                'slug' => $article->slug,
                'createdAt' => $article->created_at->toJSON(),
                'updatedAt' => $article->updated_at->toJSON(),
            ],
            'links' => ['self' => route('api.v1.articles.show', $article)],
        ])->all(),
    ]);
```

### Don't use named arguments in assertions

`assertJson(value: [...])` and `it(description: '...', closure: fn () => ...)` are positional — drop the labels:

```php
->assertJson([...]);
it('does the thing', function () { ... });
```