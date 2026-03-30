<?php

namespace App\Policies;

use App\Models\User;

class ArticlePolicy
{

    public function update(User $user, $article): bool
    {
        return $user->id === $article->user_id;
    }
}
