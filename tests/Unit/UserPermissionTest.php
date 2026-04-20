<?php

use App\Models\Permission;
use App\Models\User;
use Tests\TestCase;

it('can assign permission to a user', function () {
    /** @var TestCase $this */
    $user = User::factory()->create();

    $permission = Permission::factory()->create();

    $user->givePermissionTo($permission);

    expect($user->fresh()->permissions)->toHaveCount(1);
});

it('cannot assign the same permission twice', function () {
    /** @var TestCase $this */
    $user = User::factory()->create();

    $permission = Permission::factory()->create();

    $user->givePermissionTo($permission);
    $user->givePermissionTo($permission);

    expect($user->fresh()->permissions)->toHaveCount(1);
});
