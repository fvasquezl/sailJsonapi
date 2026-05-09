<?php

use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

beforeEach(function () {
    Role::findOrCreate('super-admin', 'web');
    Permission::findOrCreate('roles:store', 'web');
    app(PermissionRegistrar::class)->forgetCachedPermissions();
});

it('guests cannot create roles', function () {

    $this->jsonApi()
        ->withData([
            'type' => 'roles',
            'attributes' => ['name' => 'editor'],
        ])
        ->post(route('api.v1.roles.store'))
        ->assertUnauthorized(); // 401

    $this->assertDatabaseMissing('roles', [
        'name' => 'editor',
    ]);
});

it('users without super admin role cannot create roles', function () {

    Sanctum::actingAs(User::factory()->create());

    $this->jsonApi()
        ->withData(['type' => 'roles', 'attributes' => ['name' => 'editor']])
        ->post(route('api.v1.roles.store'))
        ->assertForbidden(); // 403

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

it('user with roles:store permission can create role', function () {
    $user = User::factory()->create();

    Sanctum::actingAs(userWithPermission('roles:store', $user));

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
    $this->assertDatabaseCount('roles', 1);
});

it('role name must be unique', function () {
    Role::findOrCreate('editor', 'web');

    Sanctum::actingAs(superAdminUser());

    $this->jsonApi()
        ->withData(['type' => 'roles', 'attributes' => ['name' => 'editor']])
        ->post(route('api.v1.roles.store'))
        ->assertUnprocessable()
        ->assertJsonFragment(['source' => ['pointer' => '/data/attributes/name']]);

    $this->assertDatabaseCount('roles', 2);
});

it('returns json errors when no data is sent', function () {
    Sanctum::actingAs(superAdminUser());

    $this->jsonApi()
        ->withData([])
        ->post(route('api.v1.roles.store'))
        ->assertStatus(400)
        ->assertJson(['errors' => [['source' => ['pointer' => '/data']]]]);
});

it('rejects guard_name in payload', function () {
    Sanctum::actingAs(superAdminUser());

    $this->jsonApi()
        ->withData([
            'type' => 'roles',
            'attributes' => ['name' => 'editor', 'guard_name' => 'sanctum'],
        ])
        ->post(route('api.v1.roles.store'))
        ->assertStatus(400);

    $this->assertDatabaseMissing('roles', ['name' => 'editor']);
});

it('rejects wrong resource type', function () {
    Sanctum::actingAs(superAdminUser());

    $this->jsonApi()
        ->withData(['type' => 'users', 'attributes' => ['name' => 'editor']])
        ->post(route('api.v1.roles.store'))
        ->assertStatus(409); // Conflict por type mismatch

    $this->assertDatabaseMissing('roles', ['name' => 'editor']);
});

it('rejects invalid name types', function (mixed $invalidName) {
    Sanctum::actingAs(superAdminUser());

    $this->jsonApi()
        ->withData(['type' => 'roles', 'attributes' => ['name' => $invalidName]])
        ->post(route('api.v1.roles.store'))
        ->assertUnprocessable()
        ->assertJsonFragment(['source' => ['pointer' => '/data/attributes/name']]);
})->with([
    'integer' => 123,
    'boolean' => true,
    'array' => [['nested' => 'value']],
]);

it('name has max length', function () {
    Sanctum::actingAs(superAdminUser());

    $this->jsonApi()
        ->withData(['type' => 'roles', 'attributes' => ['name' => str_repeat('a', 126)]])
        ->post(route('api.v1.roles.store'))
        ->assertUnprocessable()
        ->assertJsonFragment(['source' => ['pointer' => '/data/attributes/name']]);
});
