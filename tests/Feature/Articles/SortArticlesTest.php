<?php

use App\Models\Article;
use Tests\TestCase;

it('can sort articles by title asc', function () {
    /** @var TestCase $this */

    Article::factory()->create(['title' => 'C title']);
    Article::factory()->create(['title' => 'A title']);
    Article::factory()->create(['title' => 'B title']);

    $url = route('api.v1.articles.index', ['sort' => 'title']);

    $this->getJson($url)->assertSeeInOrder([
        'A title',
        'B title',
        'C title',
    ]);
});

it('can sort articles by title desc', function () {
    /** @var TestCase $this */

    Article::factory()->create(['title' => 'C title']);
    Article::factory()->create(['title' => 'A title']);
    Article::factory()->create(['title' => 'B title']);

    $url = route('api.v1.articles.index', ['sort' => '-title']);

    $this->getJson($url)->assertSeeInOrder([
        'C title',
        'B title',
        'A title',
    ]);
});
