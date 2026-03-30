<?php

// Pest
use App\Models\Article;
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
    $data = $this->jsonData(['user' => $user]);

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

    $this->assertDatabaseMissing('articles', [
        $this->article->getRouteKeyName() => $this->article->getRouteKey(),
    ]);
});
