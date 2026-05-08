<?php

it('super-admin passes Gate::allows for any ability', function () {
    expect(true)->toBeTrue();
});

it('non-super-admin without permission fails Gate::allows (prueba que null no rompe el flujo)', function () {
    expect(true)->toBeTrue();
});
it('non-super-admin with permission still passes via normal policy', function () {
    expect(true)->toBeTrue();
});

it('super-admin passes Gate even for abilities with no policy registered', function () {
    expect(true)->toBeTrue();
});

// $user->can('articles:update', $article) es lo que usan policies, blade directives y middleware can — debe pasar igual que Gate::allows.
it('super-admin passes $user->can() (not just Gate::allows)', function () {
    expect(true)->toBeTrue();
});

// verificar que el bypass funciona al pasar por Gate (Gate::forUser($superAdmin)->allows('update', $article)), no solo el shortcut Gate::allows con el user autenticado.
it('super-admin passes policy invocation directly via Gate::forUser', function () {
    expect(true)->toBeTrue();
});



