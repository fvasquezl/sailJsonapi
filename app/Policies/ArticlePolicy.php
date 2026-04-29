<?php

namespace App\Policies;

use App\Models\Article;
use App\Models\User;

class ArticlePolicy
{

    public function store(User $user): bool
    {
        return $user->hasPermissionTo('articles:store')
            && (string) $user->getRouteKey() === (string) request()->input('data.relationships.authors.data.id');

    }

    public function update(User $user, Article $article): bool
    {
        return $user->hasPermissionTo('articles:update')
            && $article->user->is($user);

    }

    public function destroy(User $user, Article $article): bool
    {
        return $user->hasPermissionTo('articles:delete')
        && $article->user->is($user);

    }

    public function updateCategories(User $user, Article $article): bool
    {

        return $user->hasPermissionTo('articles:update-categories')
            && $article->user->is($user);

    }

    public function updateAuthors(User $user, Article $article): bool
    {

        return $user->hasPermissionTo('articles:update-authors')
            && $article->user->is($user);

    }
}
