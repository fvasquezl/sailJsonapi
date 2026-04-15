<?php


// Pest
use App\Models\User;
use Laravel\Sanctum\PersonalAccessToken;

it('can login with valid credentials', function () {

    $user = User::factory()->create();

    $response = $this->postJson(route('api.v1.login'), [
        'email' => $user->email,
        'password' => 'password',
        'device_name' => 'iPhone de '.$user->name,
    ]);

    $token = $response->json('plain-text-token');

   $this->assertNotNull(
       PersonalAccessToken::findToken($token),
       'The plain text token is invalid'
   );

});


it('cannot login with invalid credentials', function () {

    $this->postJson(route('api.v1.login'), [
        'email' => 'fvasquez@local.com',
        'password' => 'wrong-password',
        'device_name' => 'iPhone de Faustino',
    ])->assertJsonValidationErrors([
        'email',
    ]);

});

it('email is required', function () {

    $this->postJson(route('api.v1.login'), [
        'email' => '',
        'password' => 'wrong-password',
        'device_name' => 'iPhone de Faustino',
    ])->assertSee(__('validation.required', ['attribute' => 'email']))
        ->assertJsonValidationErrors([
        'email',
    ]);

});

it('email must be vaid', function () {

    $this->postJson(route('api.v1.login'), [
        'email' => 'invalid-email',
        'password' => 'wrong-password',
        'device_name' => 'iPhone de Faustino',
    ])->assertSee(__('validation.email', ['attribute' => 'email']))
        ->assertJsonValidationErrors([
        'email',
    ]);

});

it('password is required', function () {

    $this->postJson(route('api.v1.login'), [
        'email' => 'fvasquez@local.com',
        'password' => '',
        'device_name' => 'iPhone de Faustino',
    ])->assertJsonValidationErrors(__('password'));

});

it('device_name is required', function () {

    $this->postJson(route('api.v1.login'), [
        'email' => 'fvasquez@local.com',
        'password' => 'password',
        'device_name' => '',
    ])->assertJsonValidationErrors(__('device_name'));

});

it('can login twice', function () {
    /** @var TestCase $this */
    $user = User::factory()->create();

    $token = $user->createToken($user->name)->plainTextToken;

    $this->withHeader('Authorization', 'Bearer '.$token)
        ->postJson(route('api.v1.login'))
        ->assertStatus(204);

    $this->assertNull(PersonalAccessToken::find($token));

});

