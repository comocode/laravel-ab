<?php

namespace ComoCode\LaravelAb;

use ComoCode\LaravelAb\App\Ab;
use ComoCode\LaravelAb\App\Console\Commands\AbMigrate;
use ComoCode\LaravelAb\App\Console\Commands\AbRollback;
use ComoCode\LaravelAb\App\Http\Middleware\LaravelAbMiddleware;
use Illuminate\Database\Migrations\Migrator;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class LaravelAbServiceProvider extends ServiceProvider
{

    /**
     * @var Migrator
     */
    private $migrator;


    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->make('Illuminate\Contracts\Http\Kernel')->prependMiddleware('\ComoCode\LaravelAb\App\Http\Middleware\LaravelAbMiddleware');
        $this->app->bind('Ab', 'ComoCode\LaravelAb\App\Ab');
        $this->registerCompiler();
        $this->registerCommands();

    }

    public function registerCommands(){

        $this->app->singleton('command.ab.migrate', function($app) {
            return new AbMigrate();
        });


        $this->app->singleton('command.ab.rollback', function($app) {
            return new AbRollback();
        });

        $this->commands('command.ab.migrate');
        $this->commands('command.ab.rollback');

    }
    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [
            'command.ab.migrate',
            'command.ab.rollback',
        ];
    }

    public function registerCompiler(){


        Blade::extend(function($view, $compiler)
        {

            while (preg_match_all('/@ab(?:.(?!@track|@ab))+.@track\([^\)]+\)+/si', $view, $sections_matches)){
                $sections = current($sections_matches);
                foreach($sections as $block){

                    $instance_id=preg_replace('/[^0-9]/','',microtime().rand(100000,999999));

                    if(preg_match("/@ab\(([^\)]+)\)/",$block,$match)){
                        $experiment_name = preg_replace('/[^a-z0-9\_]/i','',$match[1]);
                        $instance = $experiment_name.'_'.$instance_id;
                    }
                    else {

                        throw new \Exception("Experiment with not name not allowed");
                    }

                    $copy=preg_replace('/@ab\(.([^\)]+).\)/i',"<?php \${$instance} = App::make('Ab')->experiment('{$experiment_name}'); ?>", $block);

                    $copy=preg_replace('/@condition\(([^\)]+)\)/i',"<?php \${$instance}->condition($1); ?>", $copy);

                    $copy=preg_replace('/@track\(([^\)]+)\)/i',"<?php echo \${$instance}->track($1); ?>", $copy);

                    $view = str_replace($block,$copy,$view);
                }
            }

            $view=preg_replace('/@goal\(([^\)]+)\)/i',"<?php App::make('Ab')->goal($1); ?>",$view);

            return $view;
        });

    }
}
