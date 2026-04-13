<?php

namespace App\Policies;

use App\Models\Article;
use App\Models\User;

class ArticlePolicy
{

    public function store(User $user): bool
    {
        if (request()->has('data.relationships.authors')){
            return (string) $user->getRouteKey() === request()->json('data.relationships.authors.data.id');
        }
        return true;
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
