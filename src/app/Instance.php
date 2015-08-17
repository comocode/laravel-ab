<?php namespace ComoCode\LaravelAb\App;

class Instance extends \Eloquent {

    protected $table = 'ab_instance';
	protected $fillable = ['instance','metadata','identifier'];

    public function events(){
        return $this->hasMany('ComoCode\LaravelAb\App\Events');
    }

    public function goals(){
        return $this->hasMany('ComoCode\LaravelAb\App\Goal');
    }
}