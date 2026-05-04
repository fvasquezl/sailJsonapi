<?php

use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

beforeEach(function () {
    Role::findOrCreate('super-admin', 'web');
    app(PermissionRegistrar::class)->forgetCachedPermissions();
});

it('guests cannot list permissions', function () {
    //
});

it('users without super admin role cannot list permissions', function () {
    //
});

it('super admin can list permissions', function () {
    //
});
