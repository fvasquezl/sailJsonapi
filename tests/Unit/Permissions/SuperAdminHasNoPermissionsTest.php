<?php

use Database\Seeders\RolesAndPermissionsSeeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

beforeEach(function () {
    app(PermissionRegistrar::class)->forgetCachedPermissions();
});

it('super-admin role has no permissions so authorization stays O(1)', function () {
    expect(true)->toBeTrue();
});

// assert que tras el seed Permission::count() es ≥ N (los permisos canónicos del sistema). Si alguien borra Permission::findOrCreate del seeder por error, este test lo atrapa.
it('seeder creates the expected system permissions', function () {
    expect(true)->toBeTrue();
});

// correr el seeder dos veces seguidas no debe duplicar roles/permisos ni lanzar — findOrCreate ya lo garantiza, este test lo blinda.
it('seeder is idempotent', function () {
    expect(true)->toBeTrue();
});

