<?php

// Pest
use App\Models\Category;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

it('guest users cannot create categories', function () {

    $category = Category::factory()->raw();

    $this->jsonApi()
        ->withData([
            'type'=>'categories',
            'attributes'=>$category
            ])
        ->post(route('api.v1.categories.store'))->dump()
        ->assertUnauthorized(); // 401

    $this->assertDatabaseMissing('categories', $category);
});

// Pest
it('authenticated users can create categories', function () {

    $category = Category::factory()->raw();

    $this->assertDatabaseMissing('categories', $category);

    Sanctum::actingAs(User::factory()->create());

   $this->jsonApi()
        ->withData([
            'type'=>'categories',
            'attributes'=>$category
        ])
        ->post(route('api.v1.categories.store'))
        ->assertCreated();  // 201


    $this->assertDatabaseHas('categories', [
        'name' => $category['name'],
        'slug' => $category['slug'],
    ]);
});

// Pest
it('name is required', function () {

    $category = Category::factory()->raw(['name' => '']);

    Sanctum::actingAs(User::factory()->create());

    $this->jsonApi()
        ->withData([
            'type'=>'categories',
            'attributes'=>$category
        ])
        ->post(route('api.v1.categories.store'))
        ->assertUnprocessable() // 422
        ->assertSee('data\/attributes\/name');

    $this->assertDatabaseMissing('categories',$category);
});

// Pest
it('slug is required', function () {

    $category = Category::factory()->raw(['slug' => '']);

    Sanctum::actingAs(User::factory()->create());

    $this->jsonApi()
        ->withData([
            'type'=>'categories',
            'attributes'=>$category
        ])
        ->post(route('api.v1.categories.store'))
        ->assertUnprocessable() // 422
        ->assertSee('data\/attributes\/slug');

    $this->assertDatabaseMissing('categories',$category);
});

// Pest
it('slug must be unique', function () {

    Category::factory()->create(['slug' => 'same-slug']);

    $category = Category::factory()->raw(['slug' => 'same-slug']);

    Sanctum::actingAs(User::factory()->create());

    $this->jsonApi()
        ->withData([
            'type'=>'categories',
            'attributes'=>$category
        ])
        ->post(route('api.v1.categories.store'))
        ->assertUnprocessable() // 422
        ->assertSee('data\/attributes\/slug');

    $this->assertDatabaseMissing('categories',$category);

});

// Pest
it('slug must only contain letters numbers and dashes', function () {

    $category = Category::factory()->raw(['slug' => '%$%#@']);

    Sanctum::actingAs(User::factory()->create());

    $this->jsonApi()
        ->withData([
            'type'=>'categories',
            'attributes'=>$category
        ])
        ->post(route('api.v1.categories.store'))
        ->assertUnprocessable() // 422
        ->assertSee('data\/attributes\/slug');

    $this->assertDatabaseMissing('categories',$category);

});

// Pest
it('slug must not contain underscores', function () {

    $category = Category::factory()->raw(['slug' => 'with_underscores']);

    Sanctum::actingAs(User::factory()->create());

    $this->jsonApi()
        ->withData([
            'type'=>'categories',
            'attributes'=>$category
        ])
        ->post(route('api.v1.categories.store'))
        ->assertUnprocessable() // 422
        ->assertSee('data\/attributes\/slug');

    $this->assertDatabaseMissing('categories',$category);


});

// Pest
it('slug must not start with dashes', function () {

    $category = Category::factory()->raw(['slug' => '-start-with-dash']);

    Sanctum::actingAs(User::factory()->create());

    $this->jsonApi()
        ->withData([
            'type'=>'categories',
            'attributes'=>$category
        ])
        ->post(route('api.v1.categories.store'))
        ->assertUnprocessable() // 422
        ->assertSee('data\/attributes\/slug');

    $this->assertDatabaseMissing('categories',$category);
});

// Pest
it('slug must not end with dashes', function () {

    $category = Category::factory()->raw(['slug' => 'end-with-dash-']);

    Sanctum::actingAs(User::factory()->create());

    $this->jsonApi()
        ->withData([
            'type'=>'categories',
            'attributes'=>$category
        ])
        ->post(route('api.v1.categories.store'))
        ->assertUnprocessable() // 422
        ->assertSee('data\/attributes\/slug');

    $this->assertDatabaseMissing('categories',$category);

});
