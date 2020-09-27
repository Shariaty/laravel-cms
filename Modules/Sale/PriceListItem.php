<?php

namespace Modules\Sale;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Modules\Products\Product;

class PriceListItem extends Model {

    protected $table = 'price_list_items';
    public $timestamps = true;

    use SoftDeletes;


    protected $fillable = [ 'price_list_id' , 'title' , 'sku' , 'product_id' , 'price' ];

    public function priceList()
    {
//        return $this->belongsTo( PriceList::class , 'price_list_id' , 'id')
//            ->select([
//                '*',
//                DB::raw('MAX(price_list.created_at) as created_at')
//            ])
//            ->orderBy('price_list.created_at' , 'DESC');

        return $this->hasOne( PriceList::class  , 'id', 'price_list_id')
            ->select(['*', DB::raw('MAX(price_list.created_at) as created_at')])
            ->groupBy('price_list.id')
            ->orderBy('price_list.created_at' , 'DESC');
    }


    public function product()
    {
        return $this->belongsTo(Product::class , 'product_id' , 'id');
    }
}