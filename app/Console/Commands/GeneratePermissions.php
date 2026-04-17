<?php

namespace App\Console\Commands;

use App\JsonApi\V1\Server;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('generate:permissions')]
#[Description('Generate permissions for registered api resources')]
class GeneratePermissions extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $abilities = [
            'create',
            'view',
            'update',
            'delete',
        ];

        $server = new Server(app(), 'v1');
        $resources = collect($server->schemas()->types())->toArray();

        dd($resources);

        $this->comment('Permissions generated!');
    }
}
