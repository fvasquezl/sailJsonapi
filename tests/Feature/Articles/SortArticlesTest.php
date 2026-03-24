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

it('can sort articles by title and content', function () {
    /** @var TestCase $this */

    Article::factory()->create([
        'title' => 'C title',
        'content' => 'B content'
    ]);
    Article::factory()->create([
        'title' => 'A title',
        'content' => 'C content'
    ]);
    Article::factory()->create([
        'title' => 'B title',
        'content' => 'D content'
    ]);


    $url = route('api.v1.articles.index').'?sort=title,-content';

    $this->getJson($url)->assertSeeInOrder([
        'A title',
        'B title',
        'C title',
    ]);

    $url = route('api.v1.articles.index').'?sort=-content,title';

    $this->getJson($url)->assertSeeInOrder([
        'D content',
        'C content',
        'B content',
    ]);
});
