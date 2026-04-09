<?php


// Pest
use App\Models\Category;

it('can include categories', function () {

    $category = Category::factory()->hasArticles()->create();

    $this->jsonApi()
        ->includePaths('articles')
        ->get(route('api.v1.categories.include', $category))
        ->assertSee($category->artcles[0]->title)
        ->assertJsonFragment([
            'related' => route('api.v1.categories.relationships.articles', $category),
        ])
        ->assertJsonFragment([
            'self' => route('api.v1.categories.relationships.articles.show', $category),
        ]);
});
