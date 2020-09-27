<?php

namespace Modules\Warehouse;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseInvoice extends Model {

    protected $table = 'purchase_invoice';
    public $timestamps = true;

    use SoftDeletes;

    protected $fillable = [ 'title' , 'invoice_number' , 'total_price' , 'total_qty'];

    public function goods()
    {
        return $this->hasMany( Goods::class , 'purchase_invoice_id' , 'id');
    }

}