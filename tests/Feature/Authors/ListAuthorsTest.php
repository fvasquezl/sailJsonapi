<?php

use App\Models\User;

// Pest
it('can fetch single author', function () {

    $author = User::factory()->create();

    $this->jsonApi()
        ->get(route('api.v1.authors.show', $author))
        ->assertSee($author->name);
});

// Pest
it('can fetch all authors', function () {

    $authors = User::factory()->count(3)->create();


    $this->jsonApi()
        ->get(route('api.v1.authors.index'))
        ->assertSee($authors[0]->name);
});
