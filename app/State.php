<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class State extends Model
{
    protected $table = 'z_states';
    protected $fillable = ['name' , 'p_name' , 'position'];
    use SoftDeletes;

    public function cities()
    {
        return $this->hasMany(City::class , 'state_id' , 'id');
    }

    public function country()
    {
        return $this->belongsTo(Country::class , 'country_id' , 'id');
    }

}

