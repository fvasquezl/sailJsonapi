<?php

use App\Models\Category;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

it('guest users cannot delete categories', function () {
    /** @var TestCase $this */
    $category = Category::factory()->create();

    $this->jsonApi()
        ->delete(route('api.v1.categories.destroy', $category))
        ->assertUnauthorized(); // 401
});

it('authenticated users can delete categories', function () {
    /** @var TestCase $this */
    $category = Category::factory()->create();

    Sanctum::actingAs(User::factory()->create());

    $this->jsonApi()
        ->delete(route('api.v1.categories.destroy', $category))
        ->assertNoContent(); // 204

});
