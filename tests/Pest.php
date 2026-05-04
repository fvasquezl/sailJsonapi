<?php

use App\Models\Article;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "pest()" function to bind a different classes or traits.
|
*/

pest()->extend(TestCase::class)
    ->use(RefreshDatabase::class)
    ->in('Feature', 'Unit');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/

function jsonData(Article $article, $author = true, $categories = true): array
{
    $relationships = [];

    if ($author) {
        $relationships['authors'] = [
            'data' => ['type' => 'authors', 'id' => (string) $article->user->getRouteKey()],
        ];
    }


    if ($categories) {
        $relationships['categories'] = [
            'data' => ['type' => 'categories', 'id' => (string) $article->category->getRouteKey()],
        ];
    }

    $data = [
        'type' => 'articles',
        'attributes' => [
            'title' => $article->title,
            'slug' => $article->slug,
            'content' => $article->content,
        ],
        'relationships' => $relationships,
    ];

    if ($article->exists) {
        $data['id'] = (string) $article->getRouteKey();
    }

    return $data;
}

function userWithPermission(string $permission, ?User $user = null): User
{
    $user ??= User::factory()->create();

    $user->givePermissionTo(
        Permission::findOrCreate($permission, 'web')
    );

    return $user;
}

function superAdminUser(): User
{
    $user = User::factory()->create();
    $user->assignRole('super-admin');

    return $user;
}
