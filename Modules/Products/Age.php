<?php

namespace Modules\Products;

use Illuminate\Database\Eloquent\Model;

class Age extends Model
{
    protected $table = 'products_agerange';
    protected $fillable = [ 'product_id' ,'start', 'end' ];


    public function product()
    {
        return $this->belongsTo( Product::class );
    }

}
