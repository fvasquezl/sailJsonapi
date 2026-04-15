<?php

// Pest
use Tests\TestCase;

it('can register', function () {
    /** @var TestCase $this */

    $this->postJson(route('api.v1.register'), [
        'name' => 'Faustino Vasquez',
        'email' => 'fvasquez@local.com',
        'device_name' => 'Dispositivo de Faustino',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $this->assertDatabaseHas('users', [
        'name' => 'Faustino Vasquez',
        'email' => 'fvasquez@local.com',
    ]);
});
