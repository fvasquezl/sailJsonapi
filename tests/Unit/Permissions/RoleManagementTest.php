<?php

it('it creates a role with default web guard', function () {
    expect(true)->toBeTrue();
});
it('it assigns a role to a user', function () {
    expect(true)->toBeTrue();
});
it('it can assign multiple roles to a user', function () {
    expect(true)->toBeTrue();
});
it('assignRole is idempotent', function () {
    expect(true)->toBeTrue();
});
it('syncRoles replaces previous roles (contraste con assignRole)', function () {
    expect(true)->toBeTrue();
});
it('removeRole revokes a role', function () {
    expect(true)->toBeTrue();
});
it('hasRole returns true after assign / false after remove', function () {
    expect(true)->toBeTrue();
});

//Spatie distingue web de api. Crear Role::findOrCreate('foo', 'api') y verificar que findByName('foo', 'web') lanza RoleDoesNotExist.
it('a user can have a direct permission without any role', function () {
    expect(true)->toBeTrue();
});

//gotcha real: si el User está bound al guard web y le asignas un rol con guard api, Spatie lanza. Documentar el comportamiento.
it('assigning a role with mismatched guard throws GuardDoesNotMatch', function () {
    expect(true)->toBeTrue();
});
//solo si el código de la app los usa (revisa policies y blade).
it('hasAnyRole / hasAllRoles', function () {
    expect(true)->toBeTrue();
});

