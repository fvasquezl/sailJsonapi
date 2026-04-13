<?php

namespace Tests;

use App\Models\Article;
use App\Models\Category;
use App\Models\User;

trait CreateData
{
    public Category $category;

    public Article $article;

    public function jsonData(array $attributes = []): array
    {
        $user = $attributes['user'] ?? User::factory()->create();

        $relationships = [
            'authors' => [
                'data' => ['type' => 'authors', 'id' => (string) $user->getRouteKey()],
            ],
        ];

        if (! empty($attributes['category'])) {
            $category = $attributes['category'];
            $relationships['categories'] = [
                'data' => ['type' => 'categories', 'id' => (string) $category->getRouteKey()],
            ];
        }

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
            'relationships' => $relationships,
        ];
    }
}
