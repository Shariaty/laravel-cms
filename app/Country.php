<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    protected $table = 'z_countries';

    public function states()
    {
        return $this->hasMany(State::class , 'country_id' , 'id');
    }

}

