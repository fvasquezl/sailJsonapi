<?php

namespace App\Policies;

use App\Models\Article;
use App\Models\User;

class ArticlePolicy
{
    //    public function before(User $user)
    //    {
    //        if($user->tokenCan('articles:admin')) {
    //            return true;
    //        }
    //    }

    public function store(User $user): bool
    {

        return $user->tokenCan('articles:store') &&
            (string) $user->getRouteKey() === (string) request()->input('data.relationships.authors.data.id');

    }

    public function update(User $user, Article $article): bool
    {
        return $user->tokenCan('articles:update')
            &&
            $article->user->is($user);
    }

    public function destroy(User $user, Article $article): bool
    {
        return $user->tokenCan('articles:delete')
            &&
            $article->user->is($user);
    }

    public function updateCategories(User $user, Article $article): bool
    {

        return $user->tokenCan('articles:update-categories')
            &&
            $article->user->is($user);
    }

    public function updateAuthors(User $user, Article $article): bool
    {

        return $user->tokenCan('articles:update-authors')
            &&
            $article->user->is($user);
    }
}
