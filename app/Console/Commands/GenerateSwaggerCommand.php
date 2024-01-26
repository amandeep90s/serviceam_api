<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class GenerateSwaggerCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'swagger:generate {--ver=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Regenerate docs';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        //
    }
}
