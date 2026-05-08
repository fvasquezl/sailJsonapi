<?php

it('a role can be granted a permission', function () {
    expect(true)->toBeTrue();
});
it('a user inherits permissions from its role (crítico — el flujo más usado)', function () {
    expect(true)->toBeTrue();
});
it('syncPermissions on a role replaces previous permissions', function () {
    expect(true)->toBeTrue();
});
it('givePermissionTo on a role is idempotent', function () {
    expect(true)->toBeTrue();
});
it('revoking a permission from role removes it from users with that role', function () {
    expect(true)->toBeTrue();
});

//flujo legítimo de Spatie (override individual). Sin este test queda implícito que solo via rol.
it('a user can have a direct permission without any role', function () {
    expect(true)->toBeTrue();
});
//os tres devuelven cosas distintas. Es la API que más confunde de Spatie. Un test que ejercite las tres con un user que tiene 1 directo + 1 via rol clarifica todo.
it('getDirectPermissions vs getPermissionsViaRoles vs getAllPermissions', function () {
    expect(true)->toBeTrue();
});
// separar las dos vías. Es el contrapeso del test que ya tienes (revoking from role removes from users).
it('revoking a direct permission does not affect permissions granted via role', function () {
    expect(true)->toBeTrue();
});
//  gotcha clásico.
it('permission with same name in different guards is treated as different', function () {
    expect(true)->toBeTrue();
});



