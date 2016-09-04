<?php

namespace ComoCode\LaravelAb\App\Console\Commands;

use Illuminate\Console\Command;

class AbMigrate extends Command
{
    protected $signature = 'ab:migrate
    {--force : Bypasses prompt for user confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'migrates Laravel-Ab required tables';

    /**
     * Create a new command instance.
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
            '--path' => 'vendor/comocode/laravel-ab/migrations/',
            '--force' => $this->hasOption('force') ? true : false,
        ]);

        $this->info('AB tables created successfully');
    }
}
