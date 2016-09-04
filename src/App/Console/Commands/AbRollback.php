<?php

namespace ComoCode\LaravelAb\App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\Migrations\Migrator;

class AbRollback extends Command
{
    protected $signature = 'ab:rollback';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'removes Laravel-Ab tables';
    /**
     * @var Migrator
     */
    private $migrator;

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();
        include_once realpath(__DIR__.'/../../../../migrations/2015_08_15_000001_create_ab_tables.php');
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        if ($this->confirm('Do you wish to continue? [y|N]')) {
            $migration = new \CreateAbTables();
            if ($migration->down()) {
                $this->info('AB tables destroyed successfully');
            } else {
                $this->error('Could not delete AB tables');
            }
        } else {
            $this->error('user exited, nothing done');
        }
    }
}
