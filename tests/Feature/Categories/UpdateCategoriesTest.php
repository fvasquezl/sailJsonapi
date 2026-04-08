<?php

// Pest
use App\Models\Category;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

it('guest users cannot update categories', function () {

    $category = Category::factory()->create();

    $this->jsonApi()
        ->withData([
            'type' => 'categories',
            'id' => $category->getRouteKey(),
            'attributes' => [
                'name' => 'Name changed',
                'slug' => 'slug-changed',
            ],
        ])
        ->patch(route('api.v1.categories.update', $category))
        ->assertUnauthorized(); // 401
});

it('authenticated users can update their categories', function () {

    $category = Category::factory()->create();

    Sanctum::actingAs(User::factory()->create());

    $this->jsonApi()
        ->withData([
            'type' => 'categories',
            'id' => $category->getRouteKey(),
            'attributes' => [
                'name' => 'Name changed',
                'slug' => 'slug-changed',
            ],
        ])
        ->patch(route('api.v1.categories.update', $category))
        ->assertOK(); // 200

    $this->assertDatabaseHas('categories', [
        'name' => 'Name changed',
        'slug' => 'slug-changed',
    ]);
});

it('authenticated users can update name only', function () {

    $category = Category::factory()->create();

    Sanctum::actingAs(User::factory()->create());

    $this->jsonApi()
        ->withData([
            'type' => 'categories',
            'id' => $category->getRouteKey(),
            'attributes' => [
                'name' => 'Name changed',
            ],
        ])
        ->patch(route('api.v1.categories.update', $category))
        ->assertOK(); // 200

    $this->assertDatabaseHas('categories', [
        'name' => 'Name changed',
    ]);
});

it('authenticated users can update slug only', function () {

    $category = Category::factory()->create();

    Sanctum::actingAs(User::factory()->create());

    $this->jsonApi()
        ->withData([
            'type' => 'categories',
            'id' => $category->getRouteKey(),
            'attributes' => [
                'slug' => 'slug-changed',
            ],
        ])
        ->patch(route('api.v1.categories.update', $category))
        ->assertOK(); // 200

    $this->assertDatabaseHas('categories', [
        'slug' => 'slug-changed',
    ]);
});
