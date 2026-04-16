<?php

// Pest
use App\Models\User;
use Laravel\Sanctum\PersonalAccessToken;
use Tests\TestCase;

it('can register', function () {
    /** @var TestCase $this */
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

it('name is required', function () {
    $this->postJson(route('api.v1.register'), [
        'name' => '',
        'email' => 'fvasquez@local.com',
        'device_name' => 'Dispositivo de Faustino',
        'password' => 'password',
        'password_confirmation' => 'password',
    ])->assertJsonValidationErrors('name');

});

it('email is required', function () {
    $this->postJson(route('api.v1.register'), [
        'name' => 'Faustino Vasquez',
        'email' => '',
        'device_email' => 'Dispositivo de Faustino',
        'password' => 'password',
        'password_confirmation' => 'password',
    ])->assertJsonValidationErrors('email');
});

it('email is valid', function () {
    $this->postJson(route('api.v1.register'), [
        'name' => 'Faustino Vasquez',
        'email' => 'invalid-email',
        'device_email' => 'Dispositivo de Faustino',
        'password' => 'password',
        'password_confirmation' => 'password',
    ])->assertJsonValidationErrors('email');
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

it('password is required', function () {
    $this->postJson(route('api.v1.register'), [
        'name' => 'Faustino Vasquez',
        'email' => 'fvasquez@local.com',
        'device_email' => 'Dispositivo de Faustino',
        'password' => '',
        'password_confirmation' => 'password',
    ])->assertJsonValidationErrors('password');
});

it('password must be confirmed', function () {

    $this->postJson(route('api.v1.register'), [
        'email' => 'fvasquez@local.com',
        'device_name' => 'Dispositivo de Faustino',
        'password' => 'password',
        'password_confirmation' => 'not-confirmed',
    ])->assertJsonValidationErrors('password');
});

it('device_name is required', function () {
    $this->postJson(route('api.v1.register'), [
        'email' => 'fvasquez@local.com',
        'device_name' => '',
        'password' => 'password',
    ])->assertJsonValidationErrors('device_name');
});

