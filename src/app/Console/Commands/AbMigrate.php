<?php

namespace ComoCode\LaravelAb\App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;


class AbMigrate extends Command
{

    protected $signature = 'ab:migrate
    {--force : Bypasses prompts for confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'migrates Laravel-Ab required tables';

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
            '--path' => str_replace(base_path(),'',realpath(__DIR__.'/../../../../migrations/')),
            '--force'=>$this->hasOption('force') ? true : false
        ]);

        $this->info("AB tables created successfully");

    }
}
