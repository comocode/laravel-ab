<?php

namespace ComoCode\LaravelAb\App;

class Instance extends \Eloquent
{
    protected $table = 'ab_instance';
    protected $fillable = ['instance', 'metadata', 'identifier'];

    public function events()
    {
        return $this->hasMany('ComoCode\LaravelAb\App\Events');
    }

    public function goals()
    {
        return $this->hasMany('ComoCode\LaravelAb\App\Goal');
    }

    public function setMetadataAttribute($value)
    {
        $this->attributes['metadata'] = is_null($value) ? null : serialize($value);
    }

    public function getMetadataAttribute($value)
    {
        return unserialize($value);
    }
}
