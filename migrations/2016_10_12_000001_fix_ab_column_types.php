<?php

use Illuminate\Database\Migrations\Migration;

class FixAbColumnTypes extends Migration {
    
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Config::get('database')['default'] === 'mysql') {
            DB::statement('alter table ab_events change column instance_id instance_id integer(11)');
            DB::statement('alter table ab_events change column experiments_id experiments_id integer(11)');
    
            DB::statement('alter table ab_goal change column instance_id instance_id integer(11)');
        } else if (Config::get('database')['default'] === 'pgsql') {
            DB::statement('alter table ab_events alter column instance_id type integer using (instance_id::integer)');
            DB::statement('alter table ab_events alter column experiments_id type integer using (experiments_id::integer)');
            
            DB::statement('alter table ab_goal alter column instance_id type integer using (instance_id::integer)');
        }
    }
    
    
    /**
     * Reverse the migrations.
     *
     * @return boolean
     */
    public function down()
    {
        if (Config::get('database')['default'] === 'mysql') {
            DB::statement('alter table ab_events change column instance_id instance_id varchar(255)');
            DB::statement('alter table ab_events change column experiments_id experiments_id varchar(255)');
    
            DB::statement('alter table ab_goal change column instance_id instance_id varchar(255)');
            
        } else if (Config::get('database')['default'] === 'pgsql') {
            DB::statement('alter table ab_events alter column instance_id type varchar using (instance_id::varchar)');
            DB::statement('alter table ab_events alter column experiments_id type varchar using (experiments_id::varchar)');
        
            DB::statement('alter table ab_goal alter column instance_id type varchar using (instance_id::varchar)');
        }
        
        DB::connection()->getPdo()->exec("delete from migrations where migration = '2016_10_12_000001_fix_ab_column_types'");
        
        return true;
    }
    
}


