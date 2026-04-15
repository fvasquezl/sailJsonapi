<?php

// Pest
use App\Models\Article;
use App\Models\Category;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

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
    $category = Category::factory()->create();

    Sanctum::actingAs($user = $article->user, ['articles:update']);

    $this->jsonApi()
        ->withData([
            'type' => 'articles',
            'id' => $article->getRouteKey(),
            'attributes' => [
                'title' => 'Title changed',
                'slug' => 'slug-changed',
                'content' => 'Content changed',
            ],
            'relationships' => [
                'authors' => [
                    'data' => [
                        'type' => 'authors',
                        'id' => $user->getRouteKey(),
                    ],
                ],
                'categories' => [
                    'data' => [
                        'type' => 'categories',
                        'id' => $category->getRouteKey(),
                    ],
                ],
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

it('authenticated users can update their articles without permissions', function () {

    $article = Article::factory()->create();
    $category = Category::factory()->create();

    Sanctum::actingAs($user = $article->user);

    $this->jsonApi()
        ->withData([
            'type' => 'articles',
            'id' => $article->getRouteKey(),
            'attributes' => [
                'title' => 'Title changed',
                'slug' => 'slug-changed',
                'content' => 'Content changed',
            ],
            'relationships' => [
                'authors' => [
                    'data' => [
                        'type' => 'authors',
                        'id' => $user->getRouteKey(),
                    ],
                ],
                'categories' => [
                    'data' => [
                        'type' => 'categories',
                        'id' => $category->getRouteKey(),
                    ],
                ],
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

it('authenticated users cannot update other articles', function () {

    $article = Article::factory()->create();

    Sanctum::actingAs(User::factory()->create());

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

    Sanctum::actingAs($article->user, ['articles:update']);

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

    Sanctum::actingAs($article->user, ['articles:update']);

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

it('can replace the categories', function () {
    $article = Article::factory()->create();
    $category = Category::factory()->create();

    Sanctum::actingAs($article->user, ['articles:update-categories']);

    $this->jsonApi()
        ->withData([
            'type' => 'categories',
            'id' => $category->getRouteKey(),
        ])
        ->patch(route('api.v1.articles.categories.update', $article))
        ->assertStatus(200);

    $this->assertDatabaseMissing('articles', [
        'category_id' => $category->getRouteKey(),
    ]);
});

it('can replace the author', function () {
    /** @var TestCase $this */
    $article = Article::factory()->create();
    $author = User::factory()->create();

    Sanctum::actingAs($article->user, ['articles:update-authors']);

    $this->jsonApi()
        ->withData([
            'type' => 'authors',
            'id' => $author->getRouteKey(),
        ])
        ->patch(route('api.v1.articles.authors.update', $article))
        ->assertStatus(200);

    $this->assertDatabaseHas('articles', [
        'user_id' => $author->id,
    ]);
});
