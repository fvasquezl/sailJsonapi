<?php


// Pest
use App\Models\Category;

it('can include articles', function () {

    $category = Category::factory()->hasArticles()->create();


    $this->jsonApi()
        ->includePaths('articles')
        ->get(route('api.v1.categories.show', $category))
        ->assertSee($category->articles[0]->title)
        ->assertJsonFragment([
            'related' => route('api.v1.categories.articles', $category),
        ])
        ->assertJsonFragment([
            'self' => route('api.v1.categories.articles.show', $category),
        ]);
});
