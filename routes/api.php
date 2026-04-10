<?php

use App\Http\Controllers\Api\V1\ArticleController;
use LaravelJsonApi\Laravel\Facades\JsonApiRoute;
use LaravelJsonApi\Laravel\Http\Controllers\JsonApiController;

JsonApiRoute::server('v1')
    ->name('api.v1.')
    ->resources(function ($server) {
        $server->resource('articles', ArticleController::class)
            ->relationships(function ($server) {
                $server->hasOne('authors')->except('replace');
            });
        $server->resource('authors', JsonApiController::class)->only('index', 'show');
        $server->resource('categories', JsonApiController::class)
        ->relationships(function ($server) {
            $server->hasMany('articles')->except('replace');
        });
    });
