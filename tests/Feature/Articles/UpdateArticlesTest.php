<?php

// Pest
use App\Models\Article;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

it('guest users cannot update articles', function () {


    $article = Article::factory()->create();

    $this->jsonApi()
        ->withData([
            'type' => 'articles',
            'id' => $article->getRouteKey(),
            'attributes' => [
                'title' => 'Title false',
                'slug' => 'slug-false',
                'content' => 'Content false',
            ],
        ])
        ->patch(route('api.v1.articles.update', $article))
        ->assertUnauthorized(); // 401
});

it('authenticated users can update their articles', function () {

    $article = Article::factory()->create();

    Sanctum::actingAs($article->user);

    $this->jsonApi()
        ->withData([
            'type' => 'articles',
            'id' => $article->getRouteKey(),
            'attributes' => [
                'title' => 'Title changed',
                'slug' => 'slug-changed',
                'content' => 'Content changed',
            ],
        ])
        ->patch(route('api.v1.articles.update', $article))
        ->assertOK(); // 200

    $this->assertDatabaseHas('articles', [
        'title' => 'Title changed',
        'slug' => 'slug-changed',
        'content' => 'Content changed',
    ]);
});

it('authenticated users cannot update other articles', function () {

    $article = Article::factory()->create();

    Sanctum::actingAs($user = User::factory()->create());

    $this->jsonApi()
        ->withData([
            'type' => 'articles',
            'id' => $article->getRouteKey(),
            'attributes' => [
                'title' => 'Title changed',
                'slug' => 'slug-changed',
                'content' => 'Content changed',
            ],
        ])
        ->patch(route('api.v1.articles.update', $article))
        ->assertForbidden(); // 403

    $this->assertDatabaseMissing('articles', [
        'title' => 'Title changed',
        'slug' => 'slug-changed',
        'content' => 'Content changed',
    ]);
});

it('authenticated users can update title only', function () {

    $article = Article::factory()->create();

    Sanctum::actingAs($article->user);

    $this->jsonApi()
        ->withData([
            'type' => 'articles',
            'id' => $article->getRouteKey(),
            'attributes' => [
                'title' => 'Title changed',
            ],
        ])
        ->patch(route('api.v1.articles.update', $article))
        ->assertOK(); // 200

    $this->assertDatabaseHas('articles', [
        'title' => 'Title changed',
    ]);
});

it('authenticated users can update slug only', function () {

    $article = Article::factory()->create();

    Sanctum::actingAs($article->user);

    $this->jsonApi()
        ->withData([
            'type' => 'articles',
            'id' => $article->getRouteKey(),
            'attributes' => [
                'slug' => 'slug-changed',
            ],
        ])
        ->patch(route('api.v1.articles.update', $article))
        ->assertOK(); // 200

    $this->assertDatabaseHas('articles', [
        'slug' => 'slug-changed',
    ]);
});
