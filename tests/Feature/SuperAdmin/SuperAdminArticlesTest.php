<?php

use App\Models\Article;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

beforeEach(function () {
    Role::findOrCreate('super-admin', 'web');
    app(PermissionRegistrar::class)->forgetCachedPermissions();
});

it('super admin can create articles on behalf of another user', function () {

    $data = jsonData(
        $article = Article::factory()->make(),
    );

    Sanctum::actingAs(superAdminUser());

    $response = $this->jsonApi()
        ->withData($data)
        ->post(route('api.v1.articles.store'))
        ->assertCreated();  // 201

    expect($response->json('data.attributes'))
        ->title->toBe($article->title)
        ->slug->toBe($article->slug)
        ->content->toBe($article->content);

    $this->assertDatabaseHas('articles', [
        'user_id' => $article->user->id,
    ]);
});

it('super admin can update any article', function () {
    $article = Article::factory()->create();
    $article->title='Articlulo actualizado';
    $article->content = 'articulo-actualizado';
 
    $data = jsonData($article);
   
    Sanctum::actingAs(superAdminUser());

    $this->jsonApi()
        ->withData($data)
        ->patch(route('api.v1.articles.update', $article))
        ->assertOK(); // 200


    $this->assertDatabaseHas('articles', [
        'title' => $article->title,
    ]);
});


it('admin can delete any article', function () {
    $article = Article::factory()->create();
    $data = jsonData($article);
   
    Sanctum::actingAs(superAdminUser());

    $this->jsonApi()
        ->withData($data)
        ->delete(route('api.v1.articles.destroy', $article))
        ->assertNoContent(); // 204
        
    $this->assertDatabaseCount('articles', 0);
});
