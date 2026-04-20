<?php

use App\Models\Permission;
use Tests\TestCase;

it('can generate permissions for registered api resources', function () {
    /** @var TestCase $this */
    $this->artisan('generate:permissions')
        ->expectsOutput('Permissions generated!')
        ->assertSuccessful();

    /** @var string $firstType */
    $firstType = collect(JsonApi::server('v1')->schemas()->types())->first();

    $expected = collect(Permission::$abilities)
        ->map(fn (string $ability) => "$firstType:$ability")
        ->all();

    $this->artisan('generate:permissions')
        ->expectsOutput('Permissions generated!')
        ->assertSuccessful();

    expect(Permission::query()->pluck('name')->all())
        ->toContain(...$expected);
});
