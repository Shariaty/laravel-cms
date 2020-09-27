<?php

namespace Modules\Products;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class OptionValue extends Model
{
    use Notifiable;

    protected $table = 'product_options_value';
    protected $fillable = ['id' , 'title', 'e_title' ];


    public function option()
    {
        return $this->belongsTo( Option::class , 'option_id' ,'id' );
    }

//    public function products()
//    {
//        return $this->belongsToMany( Product::class , 'product_option_value' , 'attribute_value_id' ,  'product_id')->withTimestamps();
//    }


}
