<?php

use App\Models\Article;
use Tests\TestCase;

it(description: 'can fetch a single article', closure: function () {
    /** @var TestCase $this */
    $article = Article::factory()->create();

    $this->jsonApi()->get(route('api.v1.articles.show', $article))
        ->assertOk()
        ->assertJson([
            'data' => [
                'type' => 'articles',
                'id' => (string) $article->getRouteKey(),
                'attributes' => [
                    'title' => $article->title,
                    'slug' => $article->slug,
                    'content' => $article->content,
                    'createdAt' => $article->created_at->toJSON(),
                    'updatedAt' => $article->updated_at->toJSON(),
                ],
                'links' => [
                    'self' => route('api.v1.articles.show', $article),
                ],
            ],
            'jsonapi' => [
                'version' => '1.0',
            ],
            'links' => [
                'self' => route('api.v1.articles.show', $article),
            ],
        ]);
});

it(description: 'can fetch all articles', closure: function () {
    /** @var TestCase $this */
    $articles = Article::factory()->count(3)->create();

    $this->jsonApi()->get(route('api.v1.articles.index'))
        ->assertOk()
        ->assertJsonCount(3, 'data')
        ->assertJson([
            'data' => [
                [
                    'type' => 'articles',
                    'id' => (string) $articles[0]->getRouteKey(),
                    'attributes' => [
                        'title' => $articles[0]->title,
                        'slug' => $articles[0]->slug,
                        'content' => $articles[0]->content,
                        'createdAt' => $articles[0]->created_at->toJSON(),
                        'updatedAt' => $articles[0]->updated_at->toJSON(),
                    ],
                    'links' => [
                        'self' => route('api.v1.articles.show', $articles[0]),
                    ],
                ],
                [
                    'type' => 'articles',
                    'id' => (string) $articles[1]->getRouteKey(),
                    'attributes' => [
                        'title' => $articles[1]->title,
                        'slug' => $articles[1]->slug,
                        'content' => $articles[1]->content,
                        'createdAt' => $articles[1]->created_at->toJSON(),
                        'updatedAt' => $articles[1]->updated_at->toJSON(),
                    ],
                    'links' => [
                        'self' => route('api.v1.articles.show', $articles[1]),
                    ],
                ],
                [
                    'type' => 'articles',
                    'id' => (string) $articles[2]->getRouteKey(),
                    'attributes' => [
                        'title' => $articles[2]->title,
                        'slug' => $articles[2]->slug,
                        'content' => $articles[2]->content,
                        'createdAt' => $articles[2]->created_at->toJSON(),
                        'updatedAt' => $articles[2]->updated_at->toJSON(),
                    ],
                    'links' => [
                        'self' => route('api.v1.articles.show', $articles[2]),
                    ],
                ],
            ],
        ]);
});
