<?php


// Pest
use App\Models\Article;

it('can include authors', function () {

    $article = Article::factory()->create();

    $this->jsonApi()
        ->includePaths('authors')
        ->get(route('api.v1.articles.show', $article))
        ->assertSee($article->user->name);
});
