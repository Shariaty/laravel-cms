<?php

namespace Modules\Products;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Attribute extends Model
{
    use Notifiable;

    protected $table = 'attributes';
    protected $fillable = [ 'title', 'e_title' ];


    public function values()
    {
        return $this->hasMany( AttributeValue::class , 'attribute_id' ,'id' );
    }

}
