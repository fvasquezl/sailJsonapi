<?php

use App\Http\Middleware\RedirectIfAuthenticated;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use LaravelJsonApi\Core\Exceptions\JsonApiException;
use LaravelJsonApi\Exceptions\ExceptionParser;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        apiPrefix: 'api/v1',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'guest' => RedirectIfAuthenticated::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->dontReport(
            JsonApiException::class,
        );
        $exceptions->render(
            ExceptionParser::make()
                ->accept(fn ($ex, $request) => $request->is('api/*'))
                ->renderable(),
        );
    })->create();
