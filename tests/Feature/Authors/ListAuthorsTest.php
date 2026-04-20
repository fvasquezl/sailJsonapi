<?php

use App\Models\User;
use Illuminate\Support\Str;
use Tests\TestCase;

// Pest
it('can fetch single author', function () {
    /** @var TestCase $this */
    $author = User::factory()->create();

    $response = $this->jsonApi()
        ->get(route('api.v1.authors.show', $author))
        ->assertSee($author->name);

    $this->assertTrue(
        Str::isUuid($response->json('data.id')),
        "The authors 'id.' must be Uuid."
    );
});

// Pest
it('can fetch all authors', function () {
    /** @var TestCase $this */
    $authors = User::factory()->count(3)->create();

    $this->jsonApi()
        ->get(route('api.v1.authors.index'))
        ->assertSee($authors[0]->name);
});
