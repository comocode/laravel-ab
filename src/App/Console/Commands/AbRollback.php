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
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        
        include_once realpath(__DIR__.'/../../../../migrations/2015_08_15_000001_create_ab_tables.php');
        include_once realpath(__DIR__.'/../../../../migrations/2016_10_12_000001_fix_ab_column_types.php');
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        if ($this->confirm('Do you wish to continue? [y|N]')) {
            
            // TODO: create a more efficient way to handle down migrations
            
            $fix_migration = new \FixAbColumnTypes();
            if ($fix_migration->down()) {
                $this->info("Column types reverted successfully");
            } else {
                $this->error("Could not revert column types");
            }
            
            $create_migration = new \CreateAbTables();
            if ($create_migration->down()) {
                $this->info("AB tables destroyed successfully");
            } else {
                $this->error("Could not delete AB tables");
            }
        } else {
            $this->error("user exited, nothing done");
        }
    }
}

