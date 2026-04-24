<?php

use App\Models\Article;
use App\Models\Category;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

it('guest users cannot create articles', function () {

    $article = $this->jsonData(
        [
            'user' => User::factory()->create(),
            'category' => Category::factory()->create(),
        ]);

    $this->jsonApi()
        ->withData($article)
        ->post(route('api.v1.articles.store'))
        ->assertUnauthorized(); // 401

    expect(Article::count())->toBe(0);

});

it('returns json errors when no data is sent', function () {

    $user = User::factory()->create();

    Sanctum::actingAs($user, ['articles:store']);

    $this->jsonApi()
        ->withData([])
        ->post(route('api.v1.articles.store'))
        ->assertStatus(400)
        ->assertJson([
            'errors' => [
                ['source' => ['pointer' => '/data']],
            ],
        ]);
});

it('authenticated users can create articles', function () {

    $user = User::factory()->create();
    $category = Category::factory()->create();

    $data = $this->jsonData([
        'user' => $user,
        'category' => $category,
    ]);

    $this->assertDatabaseMissing('articles', [
        $this->article->getRouteKeyName() => $this->article->getRouteKey(),
    ]);

    Sanctum::actingAs($user, ['articles:store']);

    $response = $this->jsonApi()
        ->withData($data)
        ->post(route('api.v1.articles.store'))
        ->assertCreated();  // 201

    expect($response->json('data.attributes'))
        ->title->toBe($this->article->title)
        ->slug->toBe($this->article->slug)
        ->content->toBe($this->article->content);

    $this->assertDatabaseHas('articles', [
        'title' => $this->article->title,
        'slug' => $this->article->slug,
        'content' => $this->article->content,
        'user_id' => $user->id,
    ]);
});

it('authenticated users cannot create articles without permissions', function () {

    $user = User::factory()->create();
    $category = Category::factory()->create();

    $data = $this->jsonData([
        'user' => $user,
        'category' => $category,
    ]);

    Sanctum::actingAs($user);

    $this->jsonApi()
        ->withData($data)
        ->post(route('api.v1.articles.store'))
        ->assertForbidden();  //403 Forbidden

    expect(Article::count())->toBe(0);

});

it('authenticated users cannot create articles on behalf of other user', function () {

    $user = User::factory()->create();
    $category = Category::factory()->create();

    $data = $this->jsonData([
        'user' => $user,
        'category' => $category,
    ]);
    $data['relationships']['authors']['data']['id'] = User::factory()->create()->getRouteKey();

    $this->assertDatabaseMissing('articles', [
        $this->article->getRouteKeyName() => $this->article->getRouteKey(),
    ]);

    Sanctum::actingAs($user, ['articles:create']);

    $this->jsonApi()
        ->withData($data)
        ->post(route('api.v1.articles.store'))
        ->assertForbidden();  // 403 Prohibido

    expect(Article::count())->toBe(0);
});

it('can have protection to mass assignment', function () {

    $user = User::factory()->create();
    $category = Category::factory()->create();

    $data = $this->jsonData([
        'user' => $user,
        'category' => $category,
    ]);

    $data['attributes']['approved'] = true;

    Sanctum::actingAs($user);

    $this->jsonApi()
        ->withData($data)
        ->post(route('api.v1.articles.store'))
        ->assertStatus(400);
});

it('authors is required', function () {

    $user = User::factory()->create();
    $category = Category::factory()->create();

    $data = $this->jsonData(['category' => $category]);

    Sanctum::actingAs($user);

    $this->jsonApi()
        ->withData($data)
        ->post(route('api.v1.articles.store'))
        ->assertUnprocessable() // 422
        ->assertJsonFragment(['source' => ['pointer' => '/data/relationships/authors']]);
    $this->assertDatabaseMissing('articles', [
        $this->article->getRouteKeyName() => $this->article->getRouteKey(),
    ]);
});

it('categories is required', function () {

    $user = User::factory()->create();
    $data = $this->jsonData(['user' => $user]);

    Sanctum::actingAs($user, ['*']);

    $this->jsonApi()
        ->withData($data)
        ->post(route('api.v1.articles.store'))
        ->assertUnprocessable() // 422
        ->assertJsonFragment(['source' => ['pointer' => '/data/relationships/categories']]);
    $this->assertDatabaseMissing('articles', [
        $this->article->getRouteKeyName() => $this->article->getRouteKey(),
    ]);
});

it('relationship must be a valid type', function (string $relationship, string $wrongType) {
    $user = User::factory()->create();
    $data = $this->jsonData(['user' => $user, 'category' => Category::factory()->create()]);

    $data['relationships'][$relationship] = [
        'data' => ['type' => $wrongType, 'id' => '1'],
    ];

    Sanctum::actingAs($user);

    $this->jsonApi()
        ->withData($data)
        ->post(route('api.v1.articles.store'))
        ->assertUnprocessable()
        ->assertSee("data\\/relationships\\/$relationship");
})
    ->with([
        'authors with categories type' => ['authors', 'categories'],
        'categories with authors type' => ['categories', 'authors'],
    ]);

it('rejects empty required attributes', function (string $field) {
    $user = User::factory()->create();
    $data = $this->jsonData(['user' => $user, $field => '']);

    Sanctum::actingAs($user, ['*']);

    $this->jsonApi()
        ->withData($data)
        ->post(route('api.v1.articles.store'))
        ->assertUnprocessable()
        ->assertSee("data\\/attributes\\/$field");

    $this->assertDatabaseMissing('articles', [
        $this->article->getRouteKeyName() => $this->article->getRouteKey(),
    ]);
})
    ->with([
        'title', 'content',
    ]);

it('slug must be unique', function () {

    $user = User::factory()->create();
    $category = Category::factory()->create();

    Article::factory()->create(['slug' => 'same-slug']);

    $data = $this->jsonData([
        'user' => $user,
        'category' => $category,
        'slug' => 'same-slug',
    ]);

    Sanctum::actingAs($user, ['*']);

    $this->jsonApi()
        ->withData($data)
        ->post(route('api.v1.articles.store'))
        ->assertUnprocessable() // 422
        ->assertSee('data\/attributes\/slug');

    $this->assertDatabaseCount('articles', 1);

});

it('rejects invalid slugs', function (string $slug, ?string $translationKey = null) {
    $user = User::factory()->create();
    $data = $this->jsonData(['user' => $user, 'slug' => $slug]);

    Sanctum::actingAs($user, ['*']);

    $response = $this->jsonApi()
        ->withData($data)
        ->post(route('api.v1.articles.store'))
        ->assertUnprocessable()
        ->assertSee('data\/attributes\/slug');

    if ($translationKey) {
        $response->assertSee(__($translationKey, ['attribute' => 'slug']));
    }

    $this->assertDatabaseMissing('articles', [
        $this->article->getRouteKeyName() => $this->article->getRouteKey(),
    ]);
})
    ->with([
        'empty' => ['', null],
        'invalid characters' => ['%$%#@', null],
        'contains underscores' => ['with_underscores', 'validation.no_underscores'],
        'starts with dash' => ['-start-with-dash', 'validation.no_starting_dashes'],
        'ends with dash' => ['end-with-dash-', 'validation.no_ending_dashes'],
    ]);
