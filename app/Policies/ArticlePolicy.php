<?php

namespace App\Policies;

use App\Models\Article;
use App\Models\User;

class ArticlePolicy
{

    public function store(User $user): bool
    {

        return $user->tokenCan('articles:store') &&
            (string)$user->getRouteKey() === (string) request()->input('data.relationships.authors.data.id');

    }

    public function update(User $user, Article $article): bool
    {
        return $article->user->is($user);
    }

    public function destroy(User $user, Article $article): bool
    {
        return $article->user->is($user);
    }
}
