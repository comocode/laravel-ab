<?php

namespace ComoCode\LaravelAb\App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;


class AbMigrate extends Command
{

    protected $signature = 'ab:migrate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'migrates Laravel-Ab requried tables';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->call('migrate', [
            '--path' => str_replace(base_path(),'',realpath(__DIR__.'/../../../migrations/'))
        ]);

        $this->info("AB tables created successfully");

    }
}
