<?php

// Pest
it('can create articles', function () {

    $data = $this->jsonData();

    $this->assertDatabaseMissing('articles', [
        $this->article->getRouteKeyName() => $this->article->getRouteKey(),
    ]);

    $response = $this->jsonApi()
        ->withData($data)
        ->post(route('api.v1.articles.store'))
        ->assertCreated();  // 201

    expect($response->json('data.attributes'))
        ->title->toBe($this->article->title)
        ->slug->toBe($this->article->slug)
        ->content->toBe($this->article->content);

    $this->assertDatabaseHas('articles', $this->article->only(['title', 'slug', 'content']));
});

// Pest
it('title is required', function () {

    $data = $this->jsonData(['title' => '']);

    $this->jsonApi()
        ->withData($data)
        ->post(route('api.v1.articles.store'))
        ->assertUnprocessable(); // 422;

    $this->assertDatabaseMissing('articles', [
        $this->article->getRouteKeyName() => $this->article->getRouteKey(),
    ]);
});
