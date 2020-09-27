<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class City extends Model
{
    protected $table = 'z_cities';
    protected $fillable = ['name' , 'p_name' , 'position'];
    use SoftDeletes;


    public function state()
    {
        return $this->belongsTo(State::class , 'state_id' , 'id');
    }

}

