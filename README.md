[![Build Status](https://travis-ci.org/comocode/laravel-ab.png)](https://travis-ci.org/comocode/laravel-ab)
[![Latest Stable Version](https://poser.pugx.org/comocode/laravel-ab/v/stable)](https://packagist.org/packages/comocode/laravel-ab)
[![Total Downloads](https://poser.pugx.org/comocode/laravel-ab/downloads)](https://packagist.org/packages/comocode/laravel-ab)
[![Latest Unstable Version](https://poser.pugx.org/comocode/laravel-ab/v/unstable)](https://packagist.org/packages/comocode/laravel-ab)
[![Daily Downloads](https://poser.pugx.org/comocode/laravel-ab/d/daily)](https://packagist.org/packages/comocode/laravel-ab)

laravel-ab
==========

An A/B Testing suite for Laravel which allows multiple and nested experiments.

This will create trackable experients with as many conditions as you'd like. 
And will track conversion on each experiment based on keywords provided. 

You can have nested experiments, its conditions are regular VIEW outputs
making experiments easy to add/remove from your projects.


Usage
==========

Install using composer or which ever means you prefer

```
composer install comocode/laravel-ab 
```

then add the service provider to your app.php in config/ folder like so

```php

    ..Illuminate\Validation\ValidationServiceProvider::class,
    
    ..Illuminate\View\ViewServiceProvider::class,
    
    ComoCode\LaravelAb\LaravelAbServiceProvider::class
```

Once you have registered the service provider. You can run `php artistan`
and see the following output:
     
    ab:migrate          migrates Laravel-Ab required tables
    
    ab:rollback         removes Laravel-Ab tables
    
    ab:report <experiment>  --list outputs statistics on your current experiments or the one specified in the command
    
    
you can run ab:migrate to create the required tables, and ab:rollback to remove them anytime you wish
to view your experiment results, use the export command to see statistics


Creating Experiments
==========

There are a few PHP A/B and other Laravel packages available.

This project focuses on providing the ability to test multiple experiments
including nested experiments with a very easy to use blade interface.

```php
  </div>
    @ab('My First Experiment') ///// the name of the experiment
    @condition('ConditionOne') /// one possible condition for the experiment
    <div class="uk-grid">
        <div class="uk-width-1-1 uk-text-center">
           @ab('My Nested Experiment') /// an experiment nested within the top experiment
                @condition('NestedConditionOne')
                    <h3>Some tag</h3>
                @condition('NestedConditionTwo') /// some values
                    <h3>Some other tag</h3>
                @condition('NestedConditionThree')
                    <h3>Another tag</h3>
            @track('NestedGoal') /// the goal to track this experiment to
        </div>
    </div>
    @condition('ConditionTwo')/// condition for top level test
        <h1> other stuff </h1>
    @track('TopLevelGoal') /// goal for top level test

```
to reach an event simply do
```
  @goal('NestedGoal')   

```
in the targed page or by utilizing app()->make('Ab')->goal('NestedGoal') anywhere in your application execution.


### Weighted Conditions 

if you would like to throttle the decision towards specific conditions you can add a declaration to control the distribution. 
For example
```php
 @ab('My Nested Experiment') /// an experiment nested within the top experiment
    @condition('NestedConditionOne [2]')
        <h3>Some tag</h3>
    @condition('NestedConditionTwo [1]') /// some values
        <h3>Some other tag</h3>
    @condition('NestedConditionThree [1]')
        <h3>Another tag</h3>
@track('NestedGoal') /// the goal to track this experiment to
```

Will randomly select a result but will calculate the odd of the result based on the sum of the weights (1 + 1 + 2 = 4)  vs its specific weight 2/4, 1/4 1/4.


Results
==========
Once an experiment is executed, it will remember the options provided to the user so experiment choice selections do not change upon revisiting your project. 

A experiment is recorded per instance and goals are tracked to the instance allowing for aggregation on results per condition. 

Contributing
==========
Please feel free to contribute as A/B testing is an important part for any organization. 


TODO
==========
Add queable job to send reports on cron
Add HTML charts
