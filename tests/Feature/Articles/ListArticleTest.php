<?php

use App\Models\Article;

it('can fetch a single article', function () {
    $article = Article::factory()->create();

    $this->getJson(route('api.v1.articles.show', $article))
        ->assertOk()
        ->assertExactJson([
            'data' => [
                'type' => 'articles',
                'id' => (string)$article->getRouteKey(),
                'attributes' => [
                    'title' => $article->title,
                    'slug' => $article->slug,
                    'content' => $article->content,
                ],
                'links' => [
                    'self' => route('api.v1.articles.show', $article),
                ],
            ],
        ]);
});

it('can fetch all articles', function () {
    $articles = Article::factory()->count(3)->create();

    $this->getJson(route('api.v1.articles.index'))
        ->assertOk()
        ->assertJsonCount(3, 'data')
        ->assertExactJson([
            'data' => [
                [
                    'type' => 'articles',
                    'id' => (string)$articles[0]->getRouteKey(),
                    'attributes' => [
                        'title' => $articles[0]->title,
                        'slug' => $articles[0]->slug,
                        'content' => $articles[0]->content,
                    ],
                    'links' => [
                        'self' => route('api.v1.articles.show', $articles[0]),
                    ],
                ],
                [
                    'type' => 'articles',
                    'id' => (string)$articles[1]->getRouteKey(),
                    'attributes' => [
                        'title' => $articles[1]->title,
                        'slug' => $articles[1]->slug,
                        'content' => $articles[1]->content,
                    ],
                    'links' => [
                        'self' => route('api.v1.articles.show', $articles[1]),
                    ],
                ],
                [
                    'type' => 'articles',
                    'id' => (string)$articles[2]->getRouteKey(),
                    'attributes' => [
                        'title' => $articles[2]->title,
                        'slug' => $articles[2]->slug,
                        'content' => $articles[2]->content,
                    ],
                    'links' => [
                        'self' => route('api.v1.articles.show', $articles[2]),
                    ],
                ],
            ],
            'links' => [
                'self' => route('api.v1.articles.index'),
            ]
        ]);
});
