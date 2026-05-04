<?php

use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

beforeEach(function () {
    Role::findOrCreate('super-admin', 'web');
    app(PermissionRegistrar::class)->forgetCachedPermissions();
});

it('guests cannot create roles', function () {
    //
});

it('users without super admin role cannot create roles', function () {
    //
});

it('super admin can create a role', function () {
    //
});

it('role name is required', function () {
    //
});

it('role name must be unique', function () {
    //
});
