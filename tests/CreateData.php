<?php

namespace Tests;

use App\Models\Article;
use App\Models\Category;
use App\Models\User;

trait CreateData
{
    public User $user;

    public Category $category;

    public Article $article;

    public function jsonData(array $attributes = []): array
    {
        $this->user = $attributes['user'] ?? User::factory()->create();
        $this->category = $attributes['category'] ?? Category::factory()->create();
        $this->article = Article::factory()->make(
            array_diff_key($attributes, ['user' => true, 'category' => true])
        );

        return [
            'type' => 'articles',
            'attributes' => [
                'title' => $this->article->title,
                'slug' => $this->article->slug,
                'content' => $this->article->content,
            ],
            'relationships' => [
                'category' => [
                    'data' => ['type' => 'categories', 'id' => (string) $this->category->id],
                ],
                'user' => [
                    'data' => ['type' => 'users', 'id' => (string) $this->user->id],
                ],
            ],
        ];

    }
}
