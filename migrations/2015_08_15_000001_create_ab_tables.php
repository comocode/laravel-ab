<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAbTables extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ab_experiments', function(Blueprint $table)
        {
            $table->increments('id');
            $table->string('experiment');
            $table->string('goal')->nullable();
            $table->timestamps();
        });

        Schema::create('ab_events', function(Blueprint $table)
        {
            $table->increments('id');
            $table->string('instance_id')->nullable();
            $table->string('experiments_id')->nullable();
            $table->string('name');
            $table->string('value');
            $table->timestamps();
        });

        Schema::create('ab_instance', function(Blueprint $table)
        {
            $table->increments('id');
            $table->string('instance');
            $table->string('identifier');
            $table->string('metadata')->nullable();
            $table->timestamps();
        });

        Schema::create('ab_goal', function(Blueprint $table)
        {
            $table->increments('id');
            $table->string('instance_id')->nullable();
            $table->string('goal');
            $table->string('value')->nullable();
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     *
     * @return boolean
     */
    public function down()
    {
        Schema::drop('ab_experiments');
        Schema::drop('ab_events');
        Schema::drop('ab_instance');
        Schema::drop('ab_goal');

        \DB::connection()->getPdo()->exec("delete from migrations where migration = '2015_08_15_000001_create_ab_tables'");
        return true;
    }

}

