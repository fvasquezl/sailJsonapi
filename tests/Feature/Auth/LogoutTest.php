<?php

// Pest
use App\Models\User;
use Laravel\Sanctum\PersonalAccessToken;
use Tests\TestCase;

it('can logout', function () {
    /** @var TestCase $this */
    $user = User::factory()->create();

    $token = $user->createToken($user->name)->plainTextToken;

    $this->withHeader('Authorization', 'Bearer '.$token)
        ->postJson(route('api.v1.logout'))
        ->assertStatus(204);

    $this->assertNull(PersonalAccessToken::find($token));

});
