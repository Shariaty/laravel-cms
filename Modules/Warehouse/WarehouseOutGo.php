<?php

namespace Modules\Warehouse;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Sale\SaleInvoice;

class WarehouseOutGo extends Model {

    protected $table = 'warehouse_outgo';
    public $timestamps = true;

    use SoftDeletes;

    protected $fillable = [ 'code' ];

    public function getRouteKeyName()
    {
        return 'code';
    }

    public function wGoods()
    {
        return $this->hasMany( WGoods::class , 'warehouse_outgo_id' , 'id');
    }

    public function saleInvoice()
    {
        return $this->belongsTo(SaleInvoice::class , 'sale_invoice_id' , 'id');
    }


}