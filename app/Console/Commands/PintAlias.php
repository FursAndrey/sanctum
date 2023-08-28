<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Process;

class PintAlias extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'my:pint';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'My alias for vendor\bin\pint --preset laravel';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Process::run('vendor\bin\pint --preset laravel');
    }
}
