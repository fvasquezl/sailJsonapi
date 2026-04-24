<?php

// Pest
use App\Models\User;
use Laravel\Sanctum\PersonalAccessToken;

it('can logout', function () {

    $user = User::factory()->create();

    $token = $user->createToken($user->name)->plainTextToken;

    $this->withHeader('Authorization', 'Bearer '.$token)
        ->postJson(route('api.v1.logout'))
        ->assertStatus(204);

    $this->assertNull(PersonalAccessToken::find($token));

});
