<?php

use App\Http\Controllers\Api\LoginController;
use App\Http\Controllers\Api\RegisterController;
use App\Http\Controllers\Api\V1\ArticleController;
use Illuminate\Support\Facades\Route;
use LaravelJsonApi\Laravel\Facades\JsonApiRoute;
use LaravelJsonApi\Laravel\Http\Controllers\JsonApiController;

Route::prefix('v1')->name('api.v1.')->group(function () {
    Route::post('login', [LoginController::class, 'login'])->name('login');

    Route::post('logout', [LoginController::class, 'logout'])->name('logout')
        ->middleware('auth:sanctum');

    Route::post('register', [RegisterController::class, 'register'])->name('register')
        ;
});

JsonApiRoute::server('v1')
    ->name('api.v1.')
    ->resources(function ($server) {
        $server->resource('articles', ArticleController::class)
            ->relationships(function ($server) {
                $server->hasOne('authors');
                $server->hasOne('categories');
            });
        $server->resource('authors', JsonApiController::class)
            ->relationships(function ($server) {
                $server->hasMany('articles')->except('update', 'attach', 'detach');
            })->only('index', 'show');

        $server->resource('categories', JsonApiController::class)
            ->relationships(function ($server) {
                $server->hasMany('articles')->except('update', 'attach', 'detach');
            });

    });
