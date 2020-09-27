<?php

namespace Modules\Products;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class AttributeValue extends Model
{
    use Notifiable;

    protected $table = 'attribute_value';
    protected $fillable = ['id' , 'title', 'desc' , 'icon' , 'full_icon' ];
    protected $appends = [ 'full_icon' ];


    public function getFullIconAttribute()
    {
        $final = null;
        if($this->icon) {
            $final =  asset('uploads/admins/products/icons/'.$this->icon);
        } else {
            $final = null;
        }
        return $final ;
    }

    public function attribute()
    {
        return $this->belongsTo( Attribute::class , 'attribute_id' ,'id' );
    }

    public function products()
    {
        return $this->belongsToMany( Product::class , 'product_attribute_value' , 'attribute_value_id' ,  'product_id')->withTimestamps();
    }

}
