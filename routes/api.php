<?php

use App\Http\Controllers\Api\V1\ArticleController;
use LaravelJsonApi\Laravel\Facades\JsonApiRoute;

JsonApiRoute::server('v1')->name('api.v1.')->resources(function ($server) {
    $server->resource('articles', ArticleController::class)->only('store')->middleware('auth');
    $server->resource('articles', ArticleController::class)->except('store');
});
