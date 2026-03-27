<?php

use App\Models\Article;
use Tests\TestCase;

it('can fetch a single article', function () {
    /** @var TestCase $this */
    $article = Article::factory()->create();

    $this->getJson(route('api.v1.articles.show', $article))
        ->assertOk()
        ->assertExactJson([
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

it('can fetch all articles', function () {
    /** @var TestCase $this */
    $articles = Article::factory()->count(3)->create();

    $this->getJson(route('api.v1.articles.index'))
        ->assertOk()
        ->assertJsonCount(3, 'data')
        ->assertJsonFragment([
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
        ]);
});
