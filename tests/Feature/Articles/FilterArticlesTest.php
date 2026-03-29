<?php

use App\Models\Article;
use Tests\TestCase;

it('can filter articles by title', function () {

    /** @var TestCase $this */
    Article::factory()->create([
        'title' => 'Aprende laravel desde cero',
    ]);

    Article::factory()->create([
        'title' => 'Other Article',
    ]);

    $url = route('api.v1.articles.index', ['filter[title]' => 'Laravel']);

    $this->jsonApi()->get($url, [
        'Accept' => 'application/vnd.api+json',
        'Content-Type' => 'application/vnd.api+json',
    ])
        ->assertJsonCount(1, 'data')
        ->assertSee('Aprende laravel desde cero')
        ->assertDontSee('Other Article');
});

it('can filter articles by content', function () {

    /** @var TestCase $this */
    Article::factory()->create([
        'content' => '<div>Aprende laravel desde cero</div>',
    ]);

    Article::factory()->create([
        'content' => '<div>Other Article</div>',
    ]);

    $url = route('api.v1.articles.index', ['filter[content]' => 'Laravel']);

    $this->jsonApi()->get($url)
        ->assertJsonCount(1, 'data')
        ->assertSee('Aprende laravel desde cero')
        ->assertDontSee('Other Article');
});

it('can filter articles by year', function () {

    /** @var TestCase $this */
    Article::factory()->create([
        'title' => 'Article from 2020',
        'created_at' => now()->year(2020),
    ]);

    Article::factory()->create([
        'title' => 'Article from 2021',
        'created_at' => now()->year(2021),
    ]);

    $url = route('api.v1.articles.index', ['filter[year]' => 2020]);

    $this->jsonApi()->get($url)->assertJsonCount(1, 'data')
        ->assertSee('Article from 2020')
        ->assertDontSee('Article from 2021');
});

it('can filter articles by month', function () {

    /** @var TestCase $this */
    Article::factory()->create([
        'title' => 'Article from February',
        'created_at' => now()->startOfMonth()->setMonth(2),
    ]);

    Article::factory()->create([
        'title' => 'Another Article from February',
        'created_at' => now()->startOfMonth()->setMonth(2),
    ]);

    Article::factory()->create([
        'title' => 'Article from January',
        'created_at' => now()->startOfMonth()->setMonth(1),
    ]);

    $url = route('api.v1.articles.index', ['filter[month]' => 2]);

    $this->jsonApi()->get($url)
        ->assertJsonCount(2, 'data')
        ->assertSee('Article from February')
        ->assertSee('Another Article from February')
        ->assertDontSee('Article from January');
});

it('cannot filter articles by unknown filters', function () {

    /** @var TestCase $this */
    Article::factory()->create();

    $url = route('api.v1.articles.index', ['filter[unknown]' => 2]);

    $this->jsonApi()->get($url)->assertStatus(400);
});

it('can search articles by title and conten', function () {

    /** @var TestCase $this */
    Article::factory()->create([
        'title' => 'Article from Aprendible',
        'content' => 'Content',
    ]);

    Article::factory()->create([
        'title' => 'Another Article',
        'content' => 'Content Aprendible...',
    ]);

    Article::factory()->create([
        'title' => 'Title 2',
        'content' => 'Content 2',
    ]);

    $url = route('api.v1.articles.index', ['filter[search]' => 'Aprendible']);

    $this->jsonApi()->get($url)->assertJsonCount(2, 'data')
        ->assertSee('Article from Aprendible')
        ->assertSee('Another Article')
        ->assertDontSee('Content 2');
});

it('can search articles by title and conten with multiple terms', function () {

    /** @var TestCase $this */
    Article::factory()->create([
        'title' => 'Article from Aprendible',
        'content' => 'Content',
    ]);

    Article::factory()->create([
        'title' => 'Another Article',
        'content' => 'Content Aprendible..',
    ]);

    Article::factory()->create([
        'title' => 'Another Laravel Article',
        'content' => 'Content...',
    ]);

    Article::factory()->create([
        'title' => 'Title 2',
        'content' => 'Content 2',
    ]);

    $url = route('api.v1.articles.index', ['filter[search]' => 'Aprendible Laravel']);

    $this->jsonApi()->get($url)->assertJsonCount(3, 'data')
        ->assertSee('Article from Aprendible')
        ->assertSee('Another Article')
        ->assertSee('Another Laravel Article')
        ->assertDontSee('Content 2');
});
