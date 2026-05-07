<?php

use App\Models\Category;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

beforeEach(function () {
    Role::findOrCreate('super-admin', 'web');
    app(PermissionRegistrar::class)->forgetCachedPermissions();
});

it('super admin can create categories without explicit permission', function () {
    
    $category = Category::factory()->make();
    $data = jsonDataModel($category);

    Sanctum::actingAs(superAdminUser());

    $this->jsonApi()
        ->withData($data)
        ->post(route('api.v1.categories.store'))
        ->assertCreated();  // 201

    $this->assertDatabaseHas('categories', [
        'name' => $category->name,
        'slug' => $category->slug,
    ]);
});

it('super admin can update any category', function () {
    $category = Category::factory()->create();
    $category->name = 'category update';
    $data = jsonDataModel($category);
    Sanctum::actingAs(superAdminUser());

    $this->jsonApi()
        ->withData($data)
        ->patch(route('api.v1.categories.update', $category))
        ->assertOk();  // 200

    $this->assertDatabaseHas('categories', [
        'name' => $category->name,
    ]);
});

it('super admin can delete any category', function () {
    $category = Category::factory()->create();

    Sanctum::actingAs(superAdminUser());

    $this->jsonApi()
        ->delete(route('api.v1.categories.destroy', $category))
        ->assertNoContent(); //204

    $this->assertDatabaseCount('categories', 0);
});
