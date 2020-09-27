<?php

namespace Modules\Ratings;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Rate extends Model
{
    use Notifiable;

    protected $table = 'ratings';
    protected $fillable = ['value' , 'ratable_type' , 'rater_id'];

    public function ratable() {
        return $this->morphTo();
    }
}
