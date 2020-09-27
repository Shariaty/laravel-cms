<?php

namespace Modules\Warehouse;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Products\Product;

class WGoods extends Model {

    protected $table = 'warehouse_outgo_goods';
    public $timestamps = true;

    use SoftDeletes;

    protected $fillable = [ 'product_id' , 'unit_id' , 'quantity' ];

    public function wareHouseOutGo()
    {
        return $this->belongsTo( WarehouseOutGo::class , 'warehouse_outgo_id' , 'id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class , 'product_id' , 'id');
    }

}