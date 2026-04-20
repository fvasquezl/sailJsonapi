<?php

// Pest
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

it('can fetch the authenticated user', function () {
    /** @var TestCase $this */
    $user = User::factory()->create();

    Sanctum::actingAs($user);

    $this->getJson(route('api.v1.user'))
        ->assertJson([
            'email' => $user->email,
        ]);
});

it('guest cannot fetch any user', function () {
    /** @var TestCase $this */
    $this->getJson(route('api.v1.user'))
        ->assertUnauthorized(); // 401
});
