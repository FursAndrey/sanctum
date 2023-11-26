<?php

namespace App\Console\Commands;

use App\Jobs\TestJob;
use Illuminate\Console\Command;

class MyDebug extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'my:debug';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'My command for debug or develop';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        TestJob::dispatch()->onQueue('test_job');
        echo 'Hello World!';
    }
}
