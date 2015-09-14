<?php

\View::addNamespace('AB', base_path().'/vendor/comocode/laravel-ab/tests/source/views/');


Route::get('/weight', function () {
    return \View::make('AB::weight');
});

Route::get('/', function () {
    return \View::make('AB::nested');
});
