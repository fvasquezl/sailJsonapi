<?php

use App\Models\Article;

test('can fetch single article', function () {
    /** @var \Tests\TestCase $this */
    
    $article = Article::factory()->create();
    $response = $this->getJson("/api/v1/articles/{$article->getRouteKey()}");

    $response->assertOk();
    $response->assertJsonPath('data.attributes.title', $article->title);
});
