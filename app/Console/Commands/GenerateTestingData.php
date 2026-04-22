<?php

namespace App\Console\Commands;

use App\Models\Article;
use App\Models\Category;
use App\Models\User;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Console\ConfirmableTrait;

#[Signature('generate:testing-data')]
#[Description('Generate Testing data for the API')]
class GenerateTestingData extends Command
{
    use ConfirmableTrait;
    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (! $this->confirmToProceed()) {
            return 1;
        }

        User::query()->delete();
        Article::query()->delete();
        Category::query()->delete();

        $user = User::factory()->hasArticles(1)->create([
            'name' => 'Faustino',
            'email' => 'fvasquez@local.com',
        ]);

        $articles = Article::factory()->count(14)->create();

        $this->info('User UUID:');
        $this->line($user->id);

        $this->info('Token:');
        $this->line($user->createToken('fvasquez')->plainTextToken);

        $this->info('Article ID:');
        $this->line($user->articles->first()->slug);

        $this->info('Category ID:');
        $this->line($articles->first()->category->id);
    }
}
