<?php

use App\Models\Article;

test('can fetch single article', function () {

    /** @var \Tests\TestCase $this */

    // Arrange: crear un artículo en la base de datos
    $article = Article::factory()->create();

    // Act & Assert: solicitar el artículo y verificar la respuesta
    $this->getJson("/api/v1/articles/{$article->getRouteKey()}")
        ->assertOk()
        ->assertJsonPath('data.attributes.title', $article->title);
});
