<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class Visitor extends Model
{
    use Notifiable;
    use SoftDeletes;

    protected $table = 'visitors';
    protected $fillable = [
        'ip',
        'iso_code',
        'country',
        'city' ,
        'state',
        'state_name',
        'lat',
        'lon',
        'timezone',
        'currency'
    ];

}

