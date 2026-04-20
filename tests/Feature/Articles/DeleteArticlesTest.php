<?php

use App\Models\Article;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

it('guest users cannot delete articles', function () {
    /** @var TestCase $this */
    $article = Article::factory()->create();

    $this->jsonApi()
        ->delete(route('api.v1.articles.destroy', $article))
        ->assertUnauthorized(); // 401
});

it('authenticated users can delete their articles', function () {
    /** @var TestCase $this */
    $article = Article::factory()->create();

    Sanctum::actingAs($article->user, ['articles:delete']);

    $this->jsonApi()
        ->delete(route('api.v1.articles.destroy', $article))
        ->assertNoContent(); // 204

});

it('authenticated users can delete their articles without permissions', function () {
    /** @var TestCase $this */
    $article = Article::factory()->create();

    Sanctum::actingAs($article->user);

    $this->jsonApi()
        ->delete(route('api.v1.articles.destroy', $article))
        ->assertForbidden(); // 403

});

it('authenticated users cannot delete other articles', function () {
    /** @var TestCase $this */
    $article = Article::factory()->create();

    Sanctum::actingAs($user = User::factory()->create());

    $this->jsonApi()
        ->delete(route('api.v1.articles.destroy', $article))
        ->assertForbidden(); // 403

});
