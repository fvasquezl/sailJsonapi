<?php

use App\Models\Category;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

it('guest users cannot delete categories', function () {

    $category = Category::factory()->create();

    $this->jsonApi()
        ->delete(route('api.v1.categories.destroy', $category))
        ->assertUnauthorized(); // 401
});

it('authenticated users can delete their categories', function () {


    $category = Category::factory()->create();

    Sanctum::actingAs($category->user);

    $this->jsonApi()
        ->delete(route('api.v1.categories.destroy', $category))
        ->assertNoContent(); // 204

});

it('authenticated users cannot delete other categories', function () {

    $category = Category::factory()->create();

    Sanctum::actingAs($user = User::factory()->create());

    $this->jsonApi()
        ->delete(route('api.v1.categories.destroy', $category))
        ->assertForbidden(); // 403

});

