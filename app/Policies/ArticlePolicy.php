<?php

namespace App\Policies;

use App\Models\Article;
use App\Models\User;

class ArticlePolicy
{
    public function update(User $user, Article $article): bool
    {
        return $article->user->is($user);
    }

    public function destroy(User $user, Article $article): bool
    {
        return $article->user->is($user);
    }
}
