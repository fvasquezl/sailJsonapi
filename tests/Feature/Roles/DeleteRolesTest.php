<?php

use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

beforeEach(function () {
    Role::findOrCreate('super-admin', 'web');
    Permission::findOrCreate('roles:destroy', 'web');
    app(PermissionRegistrar::class)->forgetCachedPermissions();
});

it('guests cannot delete roles', function () {

    $role = Role::findOrCreate('editor', 'web');

    $this->jsonApi()
        ->delete(route('api.v1.roles.destroy', $role))
        ->assertUnauthorized(); // 401

    $this->assertDatabaseHas('roles', [
        'name' => 'editor',
    ]);
});

it('users without super admin role cannot delete roles', function () {
    $role = Role::findOrCreate('editor', 'web');

    Sanctum::actingAs(User::factory()->create());

    $this->jsonApi()
        ->delete(route('api.v1.roles.destroy', $role))
        ->assertForbidden(); // 403

    $this->assertDatabaseHas('roles', [
        'name' => 'editor',
    ]);
});

it('super admin can delete a role', function () {
    $role = Role::findOrCreate('editor', 'web');

    Sanctum::actingAs(superAdminUser());

    $this->jsonApi()
        ->delete(route('api.v1.roles.destroy', $role))
        ->assertNoContent(); // 204

    $this->assertDatabaseMissing('roles', [
        'name' => 'editor',
    ]);
});

it('user with roles:destroy permission can delete role', function () {
    $user = User::factory()->create();
    $role = Role::findOrCreate('editor', 'web');

    Sanctum::actingAs(userWithPermission('roles:destroy', $user));

    $this->jsonApi()
        ->delete(route('api.v1.roles.destroy', $role))
        ->assertNoContent();

    $this->assertDatabaseMissing('roles', ['name' => 'editor', 'guard_name' => 'web']);

});
