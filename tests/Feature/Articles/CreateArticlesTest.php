<?php

// Pest
use App\Models\Article;
use App\Models\Category;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

it('guest users cannot create articles', function () {
    /** @var TestCase $this */
    $article = $this->jsonData(
        [
            'user' => User::factory()->create(),
            'category' => Category::factory()->create(),
        ]);

    $this->jsonApi()
        ->withData($article)
        ->post(route('api.v1.articles.store'))
        ->assertUnauthorized(); // 401

    expect(Article::count())->toBe(0);

});

it('returns json errors when no data is sent', function () {
    /** @var TestCase $this */
    $user = User::factory()->create();

    Sanctum::actingAs($user, ['articles:store']);

    $this->jsonApi()
        ->post(route('api.v1.articles.store'))
        ->assertStatus(422)
        ->assertJson([
            'errors' => [
                ['source' => ['pointer' => '/data']],
            ],
        ]);
});

// Pest
it('authenticated users can create articles', function () {

    /** @var TestCase $this */
    $user = User::factory()->create();
    $category = Category::factory()->create();

    $data = $this->jsonData([
        'user' => $user,
        'category' => $category,
    ]);

    $this->assertDatabaseMissing('articles', [
        $this->article->getRouteKeyName() => $this->article->getRouteKey(),
    ]);

    Sanctum::actingAs($user, ['articles:store']);

    $response = $this->jsonApi()
        ->withData($data)
        ->post(route('api.v1.articles.store'))
        ->assertCreated();  // 201

    expect($response->json('data.attributes'))
        ->title->toBe($this->article->title)
        ->slug->toBe($this->article->slug)
        ->content->toBe($this->article->content);

    $this->assertDatabaseHas('articles', [
        'title' => $this->article->title,
        'slug' => $this->article->slug,
        'content' => $this->article->content,
        'user_id' => $user->id,
    ]);
});

// Pest
it('authenticated users cannot create articles without permissions', function () {
    /** @var TestCase $this */
    $user = User::factory()->create();
    $category = Category::factory()->create();

    $data = $this->jsonData([
        'user' => $user,
        'category' => $category,
    ]);

    Sanctum::actingAs($user);

    $this->jsonApi()
        ->withData($data)
        ->post(route('api.v1.articles.store'))
        ->assertStatus(403);  // Forbidden

    expect(Article::count())->toBe(0);

});

// Pest
it('authenticated users cannot create articles on behalf of other user', function () {
    /** @var TestCase $this */
    $user = User::factory()->create();
    $category = Category::factory()->create();

    $data = $this->jsonData([
        'user' => $user,
        'category' => $category,
    ]);
    $data['relationships']['authors']['data']['id'] = User::factory()->create()->getRouteKey();

    $this->assertDatabaseMissing('articles', [
        $this->article->getRouteKeyName() => $this->article->getRouteKey(),
    ]);

    Sanctum::actingAs($user, ['articles:create']);

    $this->jsonApi()
        ->withData($data)
        ->post(route('api.v1.articles.store'))
        ->assertForbidden();  // 403 Prohibido

    expect(Article::count())->toBe(0);
});

// Pest
it('can have protection to mass assignment', function () {
    /** @var TestCase $this */
    $user = User::factory()->create();
    $category = Category::factory()->create();

    $data = $this->jsonData([
        'user' => $user,
        'category' => $category,
    ]);

    $data['attributes']['approved'] = true;

    Sanctum::actingAs($user);

    $this->jsonApi()
        ->withData($data)
        ->post(route('api.v1.articles.store'))
        ->assertStatus(400);
});

// Pest
it('authors is required', function () {
    /** @var TestCase $this */
    $user = User::factory()->create();
    $category = Category::factory()->create();

    $data = $this->jsonData(['category' => $category]);

    Sanctum::actingAs($user);

    $this->jsonApi()
        ->withData($data)
        ->post(route('api.v1.articles.store'))
        ->assertUnprocessable() // 422
        ->assertJsonFragment(['source' => ['pointer' => '/data/relationships/authors']]);
    $this->assertDatabaseMissing('articles', [
        $this->article->getRouteKeyName() => $this->article->getRouteKey(),
    ]);
});

// Pest
it('categories is required', function () {
    /** @var TestCase $this */
    $user = User::factory()->create();
    $data = $this->jsonData(['user' => $user]);

    Sanctum::actingAs($user, ['*']);

    $this->jsonApi()
        ->withData($data)
        ->post(route('api.v1.articles.store'))
        ->assertUnprocessable() // 422
        ->assertJsonFragment(['source' => ['pointer' => '/data/relationships/categories']]);
    $this->assertDatabaseMissing('articles', [
        $this->article->getRouteKeyName() => $this->article->getRouteKey(),
    ]);
});

// Pest
it('authors must be a relationship object', function () {
    /** @var TestCase $this */
    $category = Category::factory()->create();
    $data = $this->jsonData(['category' => $category]);

    $data['relationships']['authors'] = [
        'data' => ['type' => 'categories', 'id' => '1'],
    ];

    Sanctum::actingAs(User::factory()->create());

    $this->jsonApi()
        ->withData($data)
        ->post(route('api.v1.articles.store'))
        ->assertStatus(422)  // documento bien formado, pero las reglas de validación del negocio no se cumplen
        ->assertSee('data\/relationships\/authors');

    $this->assertDatabaseMissing('articles', [
        $this->article->getRouteKeyName() => $this->article->getRouteKey(),

    ]);
});

// Pest
it('category must be a relationship object', function () {
    /** @var TestCase $this */
    $user = User::factory()->create();
    $data = $this->jsonData(['user' => $user]);

    $data['relationships']['categories'] = [
        'data' => ['type' => 'authors', 'id' => '1'],
    ];

    Sanctum::actingAs($user);

    $this->jsonApi()
        ->withData($data)
        ->post(route('api.v1.articles.store'))
        ->assertUnprocessable() // 422
        ->assertSee('data\/relationships\/categories');

    $this->assertDatabaseMissing('articles', [
        $this->article->getRouteKeyName() => $this->article->getRouteKey(),
    ]);
});

// Pest
it('title is required', function () {
    /** @var TestCase $this */
    $user = User::factory()->create();
    $data = $this->jsonData(['user' => $user, 'title' => '']);

    Sanctum::actingAs($user, ['*']);

    $this->jsonApi()
        ->withData($data)
        ->post(route('api.v1.articles.store'))
        ->assertUnprocessable() // 422
        ->assertSee('data\/attributes\/title');

    $this->assertDatabaseMissing('articles', [
        $this->article->getRouteKeyName() => $this->article->getRouteKey(),
    ]);
});

// Pest
it('content is required', function () {
    /** @var TestCase $this */
    $user = User::factory()->create();
    $data = $this->jsonData(['user' => $user, 'content' => '']);

    Sanctum::actingAs($user, ['*']);

    $this->jsonApi()
        ->withData($data)
        ->post(route('api.v1.articles.store'))
        ->assertUnprocessable() // 422
        ->assertSee('data\/attributes\/content');

    $this->assertDatabaseMissing('articles', [
        $this->article->getRouteKeyName() => $this->article->getRouteKey(),
    ]);
});

// Pest
it('slug is required', function () {
    /** @var TestCase $this */
    $user = User::factory()->create();
    $data = $this->jsonData(['user' => $user, 'slug' => '']);

    Sanctum::actingAs($user, ['*']);

    $this->jsonApi()
        ->withData($data)
        ->post(route('api.v1.articles.store'))
        ->assertUnprocessable() // 422
        ->assertSee('data\/attributes\/slug');

    $this->assertDatabaseMissing('articles', [
        $this->article->getRouteKeyName() => $this->article->getRouteKey(),
    ]);
});

// Pest
it('slug must be unique', function () {
    /** @var TestCase $this */
    $user = User::factory()->create();

    Article::factory()->create(['slug' => 'same-slug']);

    $data = $this->jsonData(['user' => $user, 'slug' => 'same-slug']);

    Sanctum::actingAs($user, ['*']);

    $this->jsonApi()
        ->withData($data)
        ->post(route('api.v1.articles.store'))
        ->assertUnprocessable() // 422
        ->assertSee('data\/attributes\/slug');

    $this->assertDatabaseCount('articles', 1);
});

// Pest
it('slug must only contain letters numbers and dashes', function () {
    /** @var TestCase $this */
    $user = User::factory()->create();

    $data = $this->jsonData(['user' => $user, 'slug' => '%$%#@']);

    Sanctum::actingAs($user, ['*']);

    $this->jsonApi()
        ->withData($data)
        ->post(route('api.v1.articles.store'))
        ->assertUnprocessable() // 422
        ->assertSee('data\/attributes\/slug');

    $this->assertDatabaseMissing('articles', [
        $this->article->getRouteKeyName() => $this->article->getRouteKey(),
    ]);
});

// Pest
it('slug must not contain underscores', function () {
    /** @var TestCase $this */
    $user = User::factory()->create();

    $data = $this->jsonData(['user' => $user, 'slug' => 'with_underscores']);

    Sanctum::actingAs($user, ['*']);

    $this->jsonApi()
        ->withData($data)
        ->post(route('api.v1.articles.store'))
        ->assertSee(__('validation.no_underscores', ['attribute' => 'slug']))
        ->assertUnprocessable() // 422
        ->assertSee('data\/attributes\/slug');

    $this->assertDatabaseMissing('articles', [
        $this->article->getRouteKeyName() => $this->article->getRouteKey(),
    ]);
});

// Pest
it('slug must not start with dashes', function () {
    /** @var TestCase $this */
    $user = User::factory()->create();

    $data = $this->jsonData(['user' => $user, 'slug' => '-start-with-dash']);

    Sanctum::actingAs($user, ['*']);

    $this->jsonApi()
        ->withData($data)
        ->post(route('api.v1.articles.store'))
        ->assertSee(__('validation.no_starting_dashes', ['attribute' => 'slug']))
        ->assertUnprocessable() // 422
        ->assertSee('data\/attributes\/slug');

    $this->assertDatabaseMissing('articles', [
        $this->article->getRouteKeyName() => $this->article->getRouteKey(),
    ]);
});

// Pest
it('slug must not end with dashes', function () {
    /** @var TestCase $this */
    $user = User::factory()->create();

    $data = $this->jsonData(['user' => $user, 'slug' => 'end-with-dash-']);

    Sanctum::actingAs($user, ['*']);

    $this->jsonApi()
        ->withData($data)
        ->post(route('api.v1.articles.store'))
        ->assertSee(__('validation.no_ending_dashes', ['attribute' => 'slug']))
        ->assertUnprocessable() // 422
        ->assertSee('data\/attributes\/slug');

    $this->assertDatabaseMissing('articles', [
        $this->article->getRouteKeyName() => $this->article->getRouteKey(),
    ]);
});
