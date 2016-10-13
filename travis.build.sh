#!/usr/bin/env bash

composer self-update
composer create-project laravel/laravel:5.2.31 --prefer-dist build/laravel
mkdir -p ./build/laravel/vendor/comocode
ln -s `pwd` ./build/laravel/vendor/comocode/laravel-ab
cd ./build/laravel
touch storage/database.sqlite
cp ./vendor/comocode/laravel-ab/tests/source/laravel/app.php ./config/app.php
cp ./vendor/comocode/laravel-ab/tests/source/laravel/database.php ./config/database.php
cp ./vendor/comocode/laravel-ab/tests/source/laravel/routes.php ./app/Http/routes.php
sed -e 's/"app\/"/"app\/","ComoCode\\\\LaravelAb\\\\": "vendor\/comocode\/laravel-ab\/src"/' composer.json > temp.json && mv temp.json composer.json
sed -e s/\)\;/\)/g ./vendor/composer/autoload_classmap.php > temp.php && mv temp.php ./vendor/composer/autoload_classmap.php
cat ./vendor/comocode/laravel-ab/tests/source/laravel/autoload.php >> ./vendor/composer/autoload_classmap.php
composer dump-autoload
php artisan ab:migrate --force
nohup php artisan serve &