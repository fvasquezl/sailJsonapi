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
    //
});

it('super admin can update any category', function () {
    //
});

it('super admin can delete any category', function () {
    //
});
