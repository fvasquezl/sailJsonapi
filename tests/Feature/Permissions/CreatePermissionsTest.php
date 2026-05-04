<?php

use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

beforeEach(function () {
    Role::findOrCreate('super-admin', 'web');
    app(PermissionRegistrar::class)->forgetCachedPermissions();
});

it('guests cannot create permissions', function () {
    //
});

it('users without super admin role cannot create permissions', function () {
    //
});

it('super admin can create a permission', function () {
    //
});

it('permission name is required', function () {
    //
});

it('permission name must be unique', function () {
    //
});
