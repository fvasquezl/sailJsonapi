<?php

use App\Models\User;
use Tests\TestCase;

/**
 * http://localhost/api/v1/categories?include=articles
 * http://localhost/api/v1/categories/category-slug?include=articles
 */
it('can include articles', function () {
    /** @var TestCase $this */
    $author = User::factory()->hasArticles()->create();

    $this->jsonApi()
        ->includePaths('articles')
        ->get(route('api.v1.authors.show', $author))
        ->assertSee($author->articles[0]->title)
        ->assertJsonFragment([
            'related' => route('api.v1.authors.articles', $author),
        ])
        ->assertJsonFragment([
            'self' => route('api.v1.authors.articles.show', $author),
        ]);
});

it('can fetch related articles', function () {
    /** @var TestCase $this */
    $author = User::factory()->hasArticles()->create();

    $this->jsonApi()
        ->get(route('api.v1.authors.articles', $author))
        ->assertSee($author->articles[0]->title);

    $this->jsonApi()
        ->get(route('api.v1.authors.articles.show', $author))
        ->assertSee($author->articles[0]->getRouteKey());

});
