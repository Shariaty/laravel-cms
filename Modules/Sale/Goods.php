<?php

namespace Modules\Sale;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Products\Product;

class Goods extends Model {

    protected $table = 'sale_invoice_goods';
    public $timestamps = true;

    use SoftDeletes;

    protected $fillable = ['product_id' , 'quantity' , 'final_quantity' , 'unit_id' ,'sale_price' , 'sale_invoice_id'];
//    protected $appends = ['stock' , 'unitList' , 'con'];
    protected $appends = ['unitList' , 'con'];

//    public function getStockAttribute()
//    {
////        $mojoodi = 0;
////        $tedadKharid = $this->product->purchaseGoods->sum('quantity');
////        $tedadForosh = $this->product->saleGoods->sum('final_quantity');
////        $mojoodi = $tedadKharid - $tedadForosh ;
//
////        return $this->product->saleGoods->sum('final_quantity');
//        return $this->product->purchaseGoods->sum('quantity');
//    }

    public function getUnitListAttribute()
    {
        $unitList = [];
        if($this->product->parent) {

            $unitList = array_add($unitList , $this->product->parentProduct->mainUnit->id , $this->product->parentProduct->mainUnit->title );
            if($this->product->parentProduct->subUnit) {
                $unitList = array_add($unitList , $this->product->parentProduct->subUnit->id , $this->product->parentProduct->subUnit->title);
            }
        } else {
            $unitList = array_add($unitList , $this->product->mainUnit->id , $this->product->mainUnit->title);
            if($this->product->subUnit) {
                $unitList = array_add($unitList , $this->product->subUnit->id , $this->product->subUnit->title);
            }
        }

        return $unitList;
    }

    public function getConAttribute()
    {
        $con = 0 ;
        if($this->product->parent) {
            $con = $this->product->parentProduct->conversion_factor;
        } else {
            $con = $this->product->conversion_factor;
        }
        return $con;
    }

    public function saleInvoice()
    {
        return $this->belongsTo( SaleInvoice::class , 'sale_invoice_id' , 'id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class , 'product_id' , 'id');
    }
}