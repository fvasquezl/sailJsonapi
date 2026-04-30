<?php

use App\Models\Article;
use App\Models\Category;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

beforeEach(function () {
    Permission::findOrCreate('articles:store', 'web');
    app(PermissionRegistrar::class)->forgetCachedPermissions();
});

it('guest users cannot create articles', function () {
    $data = jsonData(
        Article::factory()->make(),
        User::factory()->create(),
        Category::factory()->create(),
    );

    $this->jsonApi()
        ->withData($data)
        ->post(route('api.v1.articles.store'))
        ->assertUnauthorized(); // 401

    $this->assertDatabaseEmpty('articles');

});

it('returns json errors when no data is sent', function () {

    $user = User::factory()->create();

    Sanctum::actingAs($user);

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

    $data = jsonData(
        $article = Article::factory()->make(),
        $user = userWithPermission('articles:store'),
        Category::factory()->create(),
    );

    Sanctum::actingAs($user);

    $response = $this->jsonApi()
        ->withData($data)
        ->post(route('api.v1.articles.store'))
        ->assertCreated();  // 201

    expect($response->json('data.attributes'))
        ->title->toBe($article->title)
        ->slug->toBe($article->slug)
        ->content->toBe($article->content);

    $this->assertDatabaseHas('articles', [
        'title' => $article->title,
        'slug' => $article->slug,
        'content' => $article->content,
        'user_id' => $user->id,
    ]);
});

it('authenticated users cannot create articles without permissions', function () {

    $user = User::factory()->create();
    $category = Category::factory()->create();
    $article = Article::factory()->make();

    $data = jsonData($article, $user, $category);

    Sanctum::actingAs($user);

    $this->jsonApi()
        ->withData($data)
        ->post(route('api.v1.articles.store'))
        ->assertForbidden();  // 403 Forbidden

    $this->assertDatabaseEmpty('articles');

});

it('authenticated users cannot create articles on behalf of other user', function () {

    $data = jsonData(
        Article::factory()->make(),
        $user = userWithPermission('articles:store'),
        Category::factory()->create(),
    );
    $data['relationships']['authors']['data']['id'] = User::factory()->create()->getRouteKey();

    Sanctum::actingAs($user);

    $this->jsonApi()
        ->withData($data)
        ->post(route('api.v1.articles.store'))
        ->assertForbidden();  // 403 Prohibido

    $this->assertDatabaseEmpty('articles');
});

it('can have protection to mass assignment', function () {

    $data = jsonData(
        Article::factory()->make(),
        $user = userWithPermission('articles:store'),
        Category::factory()->create(),
    );

    $data['attributes']['approved'] = true;

    Sanctum::actingAs($user);

    $this->jsonApi()
        ->withData($data)
        ->post(route('api.v1.articles.store'))
        ->assertStatus(400);
});

it('authors is required', function () {

    $user = User::factory()->create();

    $data = jsonData(
        $article = Article::factory()->make(),
        category: Category::factory()->create(),
    );

    Sanctum::actingAs($user);

    $this->jsonApi()
        ->withData($data)
        ->post(route('api.v1.articles.store'))
        ->assertUnprocessable() // 422
        ->assertJsonFragment(['source' => ['pointer' => '/data/relationships/authors']]);
    $this->assertDatabaseMissing('articles', [
        $article->getRouteKeyName() => $article->getRouteKey(),
    ]);
});

it('categories is required', function () {

    $user = userWithPermission('articles:store');
    $article = Article::factory()->make();

    $data = jsonData($article, $user);

    Sanctum::actingAs($user);

    $this->jsonApi()
        ->withData($data)
        ->post(route('api.v1.articles.store'))
        ->assertUnprocessable() // 422
        ->assertJsonFragment(['source' => ['pointer' => '/data/relationships/categories']]);
    $this->assertDatabaseMissing('articles', [
        $article->getRouteKeyName() => $article->getRouteKey(),
    ]);
});

it('relationship must be a valid type', function (string $relationship, string $wrongType) {
    $user = User::factory()->create();
    $article = Article::factory()->make();

    $data = jsonData($article, $user, Category::factory()->create());

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
    $user = userWithPermission('articles:store');
    $article = Article::factory()->make([$field => '']);

    $data = jsonData($article, $user);

    Sanctum::actingAs($user);

    $this->jsonApi()
        ->withData($data)
        ->post(route('api.v1.articles.store'))
        ->assertUnprocessable()
        ->assertSee("data\\/attributes\\/$field");

    $this->assertDatabaseMissing('articles', [
        $article->getRouteKeyName() => $article->getRouteKey(),
    ]);
})
    ->with([
        'title', 'content',
    ]);

it('slug must be unique', function () {
    $user = userWithPermission('articles:store');
    $category = Category::factory()->create();
    Article::factory()->create(['slug' => 'same-slug']);
    $article = Article::factory()->make(['slug' => 'same-slug']);

    $data = jsonData($article, $user, $category);

    Sanctum::actingAs($user);

    $this->jsonApi()
        ->withData($data)
        ->post(route('api.v1.articles.store'))
        ->assertUnprocessable() // 422
        ->assertSee('data\/attributes\/slug');

    $this->assertDatabaseCount('articles', 1);

});

it('rejects invalid slugs', function (string $slug, ?string $translationKey = null) {
    $user = userWithPermission('articles:store');
    $article = Article::factory()->make(['slug' => $slug]);

    $data = jsonData($article, $user);

    Sanctum::actingAs($user);

    $response = $this->jsonApi()
        ->withData($data)
        ->post(route('api.v1.articles.store'))
        ->assertUnprocessable()
        ->assertSee('data\/attributes\/slug');

    if ($translationKey) {
        $response->assertSee(__($translationKey, ['attribute' => 'slug']));
    }

    $this->assertDatabaseMissing('articles', [
        $article->getRouteKeyName() => $article->getRouteKey(),
    ]);
})
    ->with([
        'empty' => ['', null],
        'invalid characters' => ['%$%#@', null],
        'contains underscores' => ['with_underscores', 'validation.no_underscores'],
        'starts with dash' => ['-start-with-dash', 'validation.no_starting_dashes'],
        'ends with dash' => ['end-with-dash-', 'validation.no_ending_dashes'],
    ]);
