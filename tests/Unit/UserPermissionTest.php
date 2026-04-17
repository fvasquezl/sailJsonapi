<?php

use App\Models\Permission;
use App\Models\User;

it('can assign permission to a user', function () {

    $user = User::factory()->create();

    $permission = Permission::factory()->create();

    $user->givePermissionTo($permission);

    expect($user->fresh()->permissions)->toHaveCount(1);
});


it('cannot assign the same permission twice', function () {

    $user = User::factory()->create();

    $permission = Permission::factory()->create();

    $user->givePermissionTo($permission);
    $user->givePermissionTo($permission);

    expect($user->fresh()->permissions)->toHaveCount(1);
});
