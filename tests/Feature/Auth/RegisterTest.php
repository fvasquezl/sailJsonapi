<?php

// Pest
use App\Models\User;
use Laravel\Sanctum\PersonalAccessToken;
use Laravel\Sanctum\Sanctum;
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

it('cannot register twice', function () {
    /** @var TestCase $this */
    Sanctum::actingAs(User::factory()->create());
    $this->postJson(route('api.v1.register'))
        ->assertNoContent(); // 204
});

it('name is required', function () {
    /** @var TestCase $this */
    $this->postJson(route('api.v1.register'), [
        'name' => '',
        'email' => 'fvasquez@local.com',
        'device_name' => 'Dispositivo de Faustino',
        'password' => 'password',
        'password_confirmation' => 'password',
    ])->assertJsonValidationErrors('name');

});

it('email is required', function () {
    /** @var TestCase $this */
    $this->postJson(route('api.v1.register'), [
        'name' => 'Faustino Vasquez',
        'email' => '',
        'device_email' => 'Dispositivo de Faustino',
        'password' => 'password',
        'password_confirmation' => 'password',
    ])->assertJsonValidationErrors('email');
});

it('email is valid', function () {
    /** @var TestCase $this */
    $this->postJson(route('api.v1.register'), [
        'name' => 'Faustino Vasquez',
        'email' => 'invalid-email',
        'device_email' => 'Dispositivo de Faustino',
        'password' => 'password',
        'password_confirmation' => 'password',
    ])->assertJsonValidationErrors('email');
});

it('email must be unique', function () {
    /** @var TestCase $this */
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
    /** @var TestCase $this */
    $this->postJson(route('api.v1.register'), [
        'name' => 'Faustino Vasquez',
        'email' => 'fvasquez@local.com',
        'device_email' => 'Dispositivo de Faustino',
        'password' => '',
        'password_confirmation' => 'password',
    ])->assertJsonValidationErrors('password');
});

it('password must be confirmed', function () {
    /** @var TestCase $this */
    $this->postJson(route('api.v1.register'), [
        'email' => 'fvasquez@local.com',
        'device_name' => 'Dispositivo de Faustino',
        'password' => 'password',
        'password_confirmation' => 'not-confirmed',
    ])->assertJsonValidationErrors('password');
});

it('device_name is required', function () {
    /** @var TestCase $this */
    $this->postJson(route('api.v1.register'), [
        'email' => 'fvasquez@local.com',
        'device_name' => '',
        'password' => 'password',
    ])->assertJsonValidationErrors('device_name');
});
