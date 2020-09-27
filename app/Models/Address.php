<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class Address extends Model
{
    use Notifiable;
    use SoftDeletes;

    protected $table = 'users_address';
    protected $fillable = [
        'title',
        'city',
        'address',
        'user_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class , 'user_id' , 'id');
    }

}

