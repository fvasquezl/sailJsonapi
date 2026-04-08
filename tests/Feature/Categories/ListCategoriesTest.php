<?php

use App\Models\Category;
use Tests\TestCase;

it(description: 'can fetch a single category', closure: function () {
    /** @var TestCase $this */
    $category = Category::factory()->create();

    $this->jsonApi()->get(route('api.v1.categories.show', $category))
        ->assertOk()
        ->assertJson([
            'data' => [
                'type' => 'categories',
                'id' => (string) $category->getRouteKey(),
                'attributes' => [
                    'name' => $category->name,
                    'slug' => $category->slug,
                ],
                'links' => [
                    'self' => route('api.v1.categories.show', $category),
                ],
            ],
        ]);
});

it(description: 'can fetch all categories', closure: function () {
    /** @var TestCase $this */
    $categories = Category::factory()->count(3)->create();

    $this->jsonApi()->get(route('api.v1.categories.index'))
        ->assertOk()
        ->assertJsonCount(3, 'data')
        ->assertJson([
            'data' => [
                [
                    'type' => 'categories',
                    'id' => (string) $categories[0]->getRouteKey(),
                    'attributes' => [
                        'name' => $categories[0]->name,
                        'slug' => $categories[0]->slug,
                        'createdAt' => $categories[0]->created_at->toJSON(),
                        'updatedAt' => $categories[0]->updated_at->toJSON(),
                    ],
                    'links' => [
                        'self' => route('api.v1.categories.show', $categories[0]),
                    ],
                ],
                [
                    'type' => 'categories',
                    'id' => (string) $categories[1]->getRouteKey(),
                    'attributes' => [
                        'name' => $categories[1]->name,
                        'slug' => $categories[1]->slug,
                        'createdAt' => $categories[1]->created_at->toJSON(),
                        'updatedAt' => $categories[1]->updated_at->toJSON(),
                    ],
                    'links' => [
                        'self' => route('api.v1.categories.show', $categories[1]),
                    ],
                ],
                [
                    'type' => 'categories',
                    'id' => (string) $categories[2]->getRouteKey(),
                    'attributes' => [
                        'name' => $categories[2]->name,
                        'slug' => $categories[2]->slug,
                        'createdAt' => $categories[2]->created_at->toJSON(),
                        'updatedAt' => $categories[2]->updated_at->toJSON(),
                    ],
                    'links' => [
                        'self' => route('api.v1.categories.show', $categories[2]),
                    ],
                ],
            ],
        ]);
});
