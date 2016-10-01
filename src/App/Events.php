<?php

namespace ComoCode\LaravelAb\App;

class Events extends \Eloquent
{
    protected $table = 'ab_events';
    protected $fillable = ['name', 'value'];
    protected $touches = array('instance');

    public function experiment()
    {
        return $this->belongsTo('ComoCode\LaravelAb\App\Experiment');
    }

    public function instance()
    {
        return $this->belongsTo('ComoCode\LaravelAb\App\Instance');
    }
}
