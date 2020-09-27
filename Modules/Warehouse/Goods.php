<?php

namespace Modules\Warehouse;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Goods extends Model {

    protected $table = 'purchase_invoice_goods';
    public $timestamps = true;

    use SoftDeletes;

    protected $fillable = ['product_id' , 'quantity' , 'purchase_price' ];

    public function purchaseInvoice()
    {
        return $this->belongsTo( PurchaseInvoice::class , 'purchase_invoice_id' , 'id');
    }

}