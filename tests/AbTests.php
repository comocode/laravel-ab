<?php
require ('vendor/autoload.php');
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Session;
use Illuminate\View\Compilers\BladeCompiler;

use ComoCode\LaravelAb\App\Experiments;
use ComoCode\LaravelAb\App\Instance;
use ComoCode\LaravelAb\App\Events;
use ComoCode\LaravelAb\App\Goal;
use ComoCode\LaravelAb\App\Ab;

class AbTests extends Illuminate\Foundation\Testing\TestCase
{
    public $baseUrl = 'http://localhost:8000';

    /**
     * A basic functional test example.
     *
     * @return void
     */
    public function createApplication()
    {
        $app = require __DIR__.'/../build/laravel/bootstrap/app.php';

        $app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

        return $app;
    }

    public function testDefaultCreation(){

        $ab = app()->make('Ab');
        $instance = $ab->experiment('Test');
        $instance->condition('one');
        echo "condition 1";
        $instance->condition('two');
        echo "condition 2";
        $instance->track('goal');
        $ab->goal('goal');

        Ab::saveSession();


        $experiments = Experiments::where(['experiment'=>'Test'])->get();

        $goals = Goal::where(['goal'=>'goal'])->get();

        $experiment = $experiments->first();

        $this->assertEquals($experiments->count(),1);
        $this->assertEquals($experiment->events()->count(), 1);

        $this->assertEquals($goals->count(),1);

    }

    public function testNestedView()
    {
        $this->visit('/')
            ->see('Test-')
            ->dontSee('@ab');

        Ab::saveSession();

        $test1_experiments = Experiments::where(['experiment'=>'test1'])->get();
        $test2_experiments = Experiments::where(['experiment'=>'test2'])->get();

        $test1 = $test1_experiments->first();
        $test2 = $test2_experiments->first();

        $this->assertEquals($test1_experiments->count(), 1);
        $this->assertEquals($test1->events()->count(), 1);

        $this->assertEquals($test2_experiments->count(), 1);
        $this->assertEquals($test2->events()->count(), 1);



    }


    public function testWeighted()
    {
        $this->visit('/weight')
            ->see('YES-SEE-THIS')
            ->dontSee('DONT-SEE-THIS');
    }

    public function testMetaDataStorage()
    {
        include('source/helper.php');

        $meta = laravel_ab_meta();

        Session::forget(config('laravel-ab.cache_key'));
        Session::flush();

        $ab = app()->make('Ab');
        $ab->forceReset();
        Ab::saveSession();

        $instance = Instance::where(['instance'=>Ab::getSession()->instance])->get()->first();
        $metadata = $instance->metadata;
        $this->assertTrue(is_array($metadata));
        $this->assertEquals($metadata, $meta);


    }
}
