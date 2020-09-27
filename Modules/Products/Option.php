<?php

namespace Modules\Products;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Option extends Model
{
    use Notifiable;

    protected $table = 'product_options';
    protected $fillable = [ 'title', 'e_title' ];


    public function values()
    {
        return $this->hasMany( OptionValue::class , 'option_id' ,'id' );
    }

}
