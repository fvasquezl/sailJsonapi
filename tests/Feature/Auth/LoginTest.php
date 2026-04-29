<?php


use App\Models\User;
use Laravel\Sanctum\PersonalAccessToken;
use Spatie\Permission\Models\Permission;

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

it('user permissions are assigned as abilities to the token response', function () {
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
    $this->postJson(route('api.v1.login'), [
        'email' => 'fvasquez@local.com',
        'password' => 'wrong-password',
        'device_name' => 'iPhone de Faustino',
    ])->assertJsonValidationErrors([
        'email',
    ]);
});

it('login validation', function (array $overrides, string $field) {
    $this->postJson(route('api.v1.login'), array_merge([
        'email' => 'fvasquez@local.com',
        'password' => 'password',
        'device_name' => 'iPhone de Faustino',
    ], $overrides))->assertJsonValidationErrors($field);
})->with([
    'email required' => [['email' => ''], 'email'],
    'email invalid' => [['email' => 'invalid-email'], 'email'],
    'password required' => [['password' => ''], 'password'],
    'device required' => [['device_name' => ''], 'device_name'],
]);

it('can login twice', function () {
    $user = User::factory()->create();

    $token = $user->createToken($user->name)->plainTextToken;

    $this->withHeader('Authorization', 'Bearer '.$token)
        ->postJson(route('api.v1.login'))
        ->assertNoContent(); // 204

    $this->assertNull(PersonalAccessToken::find($token));
});
