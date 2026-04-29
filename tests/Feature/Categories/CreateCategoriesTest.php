<?php

use App\Models\Category;
use Laravel\Sanctum\Sanctum;



beforeEach(function () {
    $this->user = userWithPermission('categories:store');
});

it('guest users cannot create categories', function () {

    $category = Category::factory()->raw();

    $this->jsonApi()
        ->withData([
            'type' => 'categories',
            'attributes' => $category,
        ])
        ->post(route('api.v1.categories.store'))
        ->assertUnauthorized(); // 401

    $this->assertDatabaseMissing('categories', $category);
});

it('authenticated users can create categories', function () {

    $category = Category::factory()->raw();

    $this->assertDatabaseMissing('categories', $category);

    Sanctum::actingAs($this->user);

    $this->jsonApi()
        ->withData([
            'type' => 'categories',
            'attributes' => $category,
        ])
        ->post(route('api.v1.categories.store'))
        ->assertCreated();  // 201

    $this->assertDatabaseHas('categories', [
        'name' => $category['name'],
        'slug' => $category['slug'],
    ]);
});

it('name is required', function () {

    $category = Category::factory()->raw(['name' => '']);

    Sanctum::actingAs($this->user);

    $this->jsonApi()
        ->withData([
            'type' => 'categories',
            'attributes' => $category,
        ])
        ->post(route('api.v1.categories.store'))
        ->assertUnprocessable() // 422
        ->assertSee('data\/attributes\/name');

    $this->assertDatabaseMissing('categories', $category);
});

it('slug is required', function () {

    $category = Category::factory()->raw(['slug' => '']);

    Sanctum::actingAs($this->user);

    $this->jsonApi()
        ->withData([
            'type' => 'categories',
            'attributes' => $category,
        ])
        ->post(route('api.v1.categories.store'))
        ->assertUnprocessable() // 422
        ->assertSee('data\/attributes\/slug');

    $this->assertDatabaseMissing('categories', $category);
});

it('slug must be unique', function () {

    Category::factory()->create(['slug' => 'same-slug']);

    $category = Category::factory()->raw(['slug' => 'same-slug']);

    Sanctum::actingAs($this->user);

    $this->jsonApi()
        ->withData([
            'type' => 'categories',
            'attributes' => $category,
        ])
        ->post(route('api.v1.categories.store'))
        ->assertUnprocessable() // 422
        ->assertSee('data\/attributes\/slug');

    $this->assertDatabaseMissing('categories', $category);

});

it('slug must only contain letters numbers and dashes', function () {

    $category = Category::factory()->raw(['slug' => '%$%#@']);

    Sanctum::actingAs($this->user);

    $this->jsonApi()
        ->withData([
            'type' => 'categories',
            'attributes' => $category,
        ])
        ->post(route('api.v1.categories.store'))
        ->assertUnprocessable() // 422
        ->assertSee('data\/attributes\/slug');

    $this->assertDatabaseMissing('categories', $category);

});

it('slug must not contain underscores', function () {

    $category = Category::factory()->raw(['slug' => 'with_underscores']);

    Sanctum::actingAs($this->user);

    $this->jsonApi()
        ->withData([
            'type' => 'categories',
            'attributes' => $category,
        ])
        ->post(route('api.v1.categories.store'))
        ->assertUnprocessable() // 422
        ->assertSee('data\/attributes\/slug');

    $this->assertDatabaseMissing('categories', $category);

});

it('slug must not start with dashes', function () {

    $category = Category::factory()->raw(['slug' => '-start-with-dash']);

    Sanctum::actingAs($this->user);

    $this->jsonApi()
        ->withData([
            'type' => 'categories',
            'attributes' => $category,
        ])
        ->post(route('api.v1.categories.store'))
        ->assertUnprocessable() // 422
        ->assertSee('data\/attributes\/slug');

    $this->assertDatabaseMissing('categories', $category);
});

it('slug must not end with dashes', function () {

    $category = Category::factory()->raw(['slug' => 'end-with-dash-']);

    Sanctum::actingAs($this->user);

    $this->jsonApi()
        ->withData([
            'type' => 'categories',
            'attributes' => $category,
        ])
        ->post(route('api.v1.categories.store'))
        ->assertUnprocessable() // 422
        ->assertSee('data\/attributes\/slug');

    $this->assertDatabaseMissing('categories', $category);

});
