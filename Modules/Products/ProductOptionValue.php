<?php

namespace Modules\Products;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class ProductOptionValue extends Model
{
    use Notifiable;

    protected $table = 'products_products_options_values';
    protected $fillable = [ 'product_id', 'value_id' ];

    protected $appends = [ 'title' ];


    public function getTitleAttribute()
    {
        return $this->valueTitle->title;
    }

    public function product()
    {
        return $this->belongsTo( Product::class , 'product_id' ,'id' );
    }

    public function valueTitle(){
        return $this->hasOne(OptionValue::class, 'id' , 'value_id' );
    }

}
