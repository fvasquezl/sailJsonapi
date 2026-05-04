<?php

use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

beforeEach(function () {
    Role::findOrCreate('super-admin', 'web');
    app(PermissionRegistrar::class)->forgetCachedPermissions();
});

it('guests cannot update permissions', function () {
    //
});

it('users without super admin role cannot update permissions', function () {
    //
});

it('super admin can update a permission', function () {
    //
});
