<?php

// Pest
use App\Models\User;
use Laravel\Sanctum\PersonalAccessToken;
use Laravel\Sanctum\Sanctum;

// Pest
it('can register', function () {

    $response = $this->postJson(route('api.v1.register'), [
        'name' => 'Faustino Vasquez',
        'email' => 'fvasquez@local.com',
        'device_name' => 'Dispositivo de Faustino',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $token = $response->json('plain-text-token');

    $this->assertNotNull(
        PersonalAccessToken::findToken($token),
        'The plain-text-token for this token is invalid.'
    );

    $this->assertDatabaseHas('users', [
        'name' => 'Faustino Vasquez',
        'email' => 'fvasquez@local.com',
    ]);
});

// Pest
it('cannot register twice', function () {

    Sanctum::actingAs(User::factory()->create());
    $this->postJson(route('api.v1.register'))
        ->assertNoContent(); // 204
});

it('email must be unique', function () {

    $user = User::factory()->create();

    $this->postJson(route('api.v1.register'), [
        'name' => 'Faustino Vasquez',
        'email' => $user->email,
        'device_email' => 'Dispositivo de Faustino',
        'password' => 'password',
        'password_confirmation' => 'password',
    ])->assertJsonValidationErrors('email');
});

// Pest (common email validation)
it('validates registration field', function (array $overrides, string $field) {
    $this->postJson(route('api.v1.register'), array_merge([
        'name' => 'Faustino Vasquez',
        'email' => 'fvasquez@local.com',
        'device_name' => 'iPhone',
        'password' => 'password',
        'password_confirmation' => 'password',
    ], $overrides))->assertJsonValidationErrors($field);
})->with([
    'name required' => [['name' => ''], 'name'],
    'email required' => [['email' => ''], 'email'],
    'email invalid' => [['email' => 'not-email'], 'email'],
    'password required' => [['password' => '', 'password_confirmation' => ''], 'password'],
    'password mismatch' => [['password_confirmation' => 'other'], 'password'],
    'device_name required' => [['device_name' => ''], 'device_name'],
]);

