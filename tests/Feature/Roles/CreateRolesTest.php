<?php

use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

beforeEach(function () {
    Role::findOrCreate('super-admin', 'web');
    app(PermissionRegistrar::class)->forgetCachedPermissions();
});

it('guests cannot create roles', function () {
    $role = new Role(['name' => 'editor']);
    $data = jsonData($role);

    $this->jsonApi()->withData($data)->post(route('api.v1.roles.store'))->assertUnauthorized(); // 401

    $this->assertDatabaseMissing('roles', [
        'name' => 'editor',
    ]);
});

it('users without super admin role cannot create roles', function () {
 
    Sanctum::actingAs(User::factory()->create());

    $this->jsonApi()
    ->withData(['type' => 'roles', 'attributes' => ['name' => 'editor']])
    ->post(route('api.v1.roles.store'))
    ->assertForbidden(); //403

    $this->assertDatabaseMissing('roles', ['name' => 'editor']);
});

it('super admin can create a role', function () {
    Sanctum::actingAs(superAdminUser());

    $this->jsonApi()
        ->withData(['type' => 'roles', 'attributes' => ['name' => 'editor']])
        ->post(route('api.v1.roles.store'))
        ->assertCreated();

    $this->assertDatabaseHas('roles', ['name' => 'editor', 'guard_name' => 'web']);
});

it('role name is required', function () {
    Sanctum::actingAs(superAdminUser());

    $this->jsonApi()
        ->withData(['type' => 'roles', 'attributes' => ['name' => '']])
        ->post(route('api.v1.roles.store'))
        ->assertUnprocessable()
        ->assertJsonFragment(['source' => ['pointer' => '/data/attributes/name']]);
});

it('role name must be unique', function () {
    Role::findOrCreate('editor', 'web'); // ya existe

    Sanctum::actingAs(superAdminUser());

    $this->jsonApi()
        ->withData(['type' => 'roles', 'attributes' => ['name' => 'editor']])
        ->post(route('api.v1.roles.store'))
        ->assertUnprocessable()
        ->assertJsonFragment(['source' => ['pointer' => '/data/attributes/name']]);

    $this->assertDatabaseCount('roles', 2); // super-admin + editor (uno solo)
});
