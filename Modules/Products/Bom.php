<?php

namespace Modules\Products;


use Illuminate\Database\Eloquent\Model;

class Bom extends Model {

	protected $table = 'products_bom';
	public $timestamps = true;

    protected $fillable = [ 'product_id', 'rawProduct_id' , 'value' ];

    public function product()
    {
        return $this->belongsTo(Product::class , 'product_id' , 'id');
    }

    public function rawProduct()
    {
        return $this->belongsTo(Product::class , 'rawProduct_id' , 'id');
    }

}