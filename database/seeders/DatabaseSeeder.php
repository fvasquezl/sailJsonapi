<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $user = User::factory()->create([
            'name' => 'Faustino Vasquez Limon',
            'email' => 'fvasquez@local.com',
        ]);

        Article::factory()->count(3)->create(['user_id' => $user->id]);

        Article::factory()->count(19)->create();
    }
}
