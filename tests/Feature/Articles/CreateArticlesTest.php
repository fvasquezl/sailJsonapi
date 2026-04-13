<?php

// Pest
use App\Models\Article;
use App\Models\Category;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

it('guest users cannot create articles', function () {

    $article = $this->jsonData(['user' => null]);

    $this->jsonApi()
        ->withData($article)
        ->post(route('api.v1.articles.store'))
        ->assertUnauthorized(); // 401

    $this->assertDatabaseMissing('articles', $article);
});

// Pest
it('authenticated users can create articles', function () {

    $user = User::factory()->create();
    $category = Category::factory()->create();

    $data = $this->jsonData([
        'user' => $user,
        'category' => $category,
    ]);

    $this->assertDatabaseMissing('articles', [
        $this->article->getRouteKeyName() => $this->article->getRouteKey(),
    ]);

    Sanctum::actingAs($user);

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
it('category is required', function () {

    $user = User::factory()->create();
    $data = $this->jsonData(['user' => $user]);

    Sanctum::actingAs($user);

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
it('category must be a relationship object', function () {

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

    $user = User::factory()->create();
    $data = $this->jsonData(['user' => $user, 'title' => '']);

    Sanctum::actingAs($user);

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

    $user = User::factory()->create();
    $data = $this->jsonData(['user' => $user, 'content' => '']);

    Sanctum::actingAs($user);

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

    $user = User::factory()->create();
    $data = $this->jsonData(['user' => $user, 'slug' => '']);

    Sanctum::actingAs($user);

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

    $user = User::factory()->create();

    Article::factory()->create(['slug' => 'same-slug']);

    $data = $this->jsonData(['user' => $user, 'slug' => 'same-slug']);

    Sanctum::actingAs($user);

    $this->jsonApi()
        ->withData($data)
        ->post(route('api.v1.articles.store'))
        ->assertUnprocessable() // 422
        ->assertSee('data\/attributes\/slug');

    $this->assertDatabaseCount('articles', 1);
});

// Pest
it('slug must only contain letters numbers and dashes', function () {

    $user = User::factory()->create();

    $data = $this->jsonData(['user' => $user, 'slug' => '%$%#@']);

    Sanctum::actingAs($user);

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

    $user = User::factory()->create();

    $data = $this->jsonData(['user' => $user, 'slug' => 'with_underscores']);

    Sanctum::actingAs($user);

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

    $user = User::factory()->create();

    $data = $this->jsonData(['user' => $user, 'slug' => '-start-with-dash']);

    Sanctum::actingAs($user);

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

    $user = User::factory()->create();

    $data = $this->jsonData(['user' => $user, 'slug' => 'end-with-dash-']);

    Sanctum::actingAs($user);

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
