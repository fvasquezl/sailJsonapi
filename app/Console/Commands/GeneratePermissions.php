<?php

namespace App\Console\Commands;

use App\Models\Permission;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use LaravelJsonApi\Core\Facades\JsonApi;

#[Signature('generate:permissions')]
#[Description('Generate permissions for registered api resources')]
class GeneratePermissions extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {

        $server = JsonApi::server('v1');

        $schemas = collect($server->schemas()->types())->toArray();
        foreach ($schemas as $schema) {
            $this->comment("Permissions for $schema");
            foreach (Permission::$abilities as $ability) {
                Permission::firstOrCreate([
                    'name' => $name = "$schema:$ability",
                ]);
                $this->line("$name");
            }

        }

        $this->info('Permissions generated!');
    }
}
