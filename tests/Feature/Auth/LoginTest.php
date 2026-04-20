<?php

// Pest
use App\Models\Permission;
use App\Models\User;
use Laravel\Sanctum\PersonalAccessToken;
use Tests\TestCase;

it('can login with valid credentials', function () {
    /** @var TestCase $this */
    $user = User::factory()->create();

    $response = $this->postJson(route('api.v1.login'), [
        'email' => $user->email,
        'password' => 'password',
        'device_name' => 'iPhone de '.$user->name,
    ]);

    $token = $response->json('plain-text-token');

    $this->assertNotNull(
        $dbToken = PersonalAccessToken::findToken($token),
        'The plain text token is invalid'
    );

});

it('user permissions are assigned as abilities to the token response', function () {
    /** @var TestCase $this */
    $user = User::factory()->create();
    $permission1 = Permission::factory()->create([
        'name' => $articlesCreatePermission = 'articles:create',
    ]);
    $permission2 = Permission::factory()->create([
        'name' => $articlesUpdatePermission = 'articles:update',
    ]);

    $user->givePermissionTo($permission1);
    $user->givePermissionTo($permission2);

    $response = $this->postJson(route('api.v1.login'), [
        'email' => $user->email,
        'password' => 'password',
        'device_name' => 'iPhone de '.$user->name,
    ]);

    $dbToken = PersonalAccessToken::findToken($response->json('plain-text-token'));

    $this->assertTrue($dbToken->can($articlesCreatePermission));
    $this->assertTrue($dbToken->can($articlesUpdatePermission));
    $this->assertFalse($dbToken->can('articles:delete'));

});

it('cannot login with invalid credentials', function () {
    /** @var TestCase $this */
    $this->postJson(route('api.v1.login'), [
        'email' => 'fvasquez@local.com',
        'password' => 'wrong-password',
        'device_name' => 'iPhone de Faustino',
    ])->assertJsonValidationErrors([
        'email',
    ]);

});

it('email is required', function () {
    /** @var TestCase $this */
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
    /** @var TestCase $this */
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
    /** @var TestCase $this */
    $this->postJson(route('api.v1.login'), [
        'email' => 'fvasquez@local.com',
        'password' => '',
        'device_name' => 'iPhone de Faustino',
    ])->assertJsonValidationErrors(__('password'));

});

it('device_name is required', function () {
    /** @var TestCase $this */
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
