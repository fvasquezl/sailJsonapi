<?php

it('can generate permissions for registered api resources', function () {
    $this->artisan('generate:permissions')
    ->expectsOutput('Permissions generated!');

    $this->assertDatabaseHas('permissions', [
        'name' => 'articles:create'
    ]);
});
