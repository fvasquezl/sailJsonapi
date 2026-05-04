<?php

use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

beforeEach(function () {
    Role::findOrCreate('super-admin', 'web');
    app(PermissionRegistrar::class)->forgetCachedPermissions();
});

it('guests cannot delete roles', function () {
    //
});

it('users without super admin role cannot delete roles', function () {
    //
});

it('super admin can delete a role', function () {
    //
});
