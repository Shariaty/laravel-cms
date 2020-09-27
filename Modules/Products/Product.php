<?php

namespace Modules\Products;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Modules\Sale\PriceListItem;
use Modules\Warehouse\Goods;
use Modules\Sale\Goods as saleGoods;
use App\Facade\Facades\OilSettings;
use Modules\Warehouse\WGoods;


class Product extends Model {

	protected $table = 'products';
	public $timestamps = true;

    use SoftDeletes;

    protected $fillable = [
        'category_id',
        'manufacture_id',
        'fake',
        'type',
        'mainUnit_id',
        'subUnit_id',
        'conversion_factor',
        'sku',
        'parent',
	    'title',
        'quantity',
        'price',
        'desc' ,
        'is_published',
        'file',
        'option_1',
        'option_2',
        'has_limit',
        'limit_value',
        'limit_time'
    ];

    protected $appends = [  'visible_sku' , 'cover_image' , 'display_price' , 'price',
                            'sub_product_count' , 'subs' , 'placeholder' ,
                            'gallery' , 'gallery_count' , 'desc_escaped' , 'limitTimeConverted'];

    public function getLimitTimeConvertedAttribute()
    {
        return getLimitTimingName($this->limit_time);
    }
    
    public function getDisplayPriceAttribute()
    {
        $final = 0 ;
//        if($this->getSubProductCountAttribute() > 0) {
//            $prices = [];
//            $products =  $this->where('parent' , $this->sku)->notFake()->published()->get();
//            foreach ($products as $product) {
//                $prices[$product->price] = $product->price;
//            }
//            asort($prices);
//            $final =  array_first($prices);
//        } else {
//            $final = $this->price ;
//        }
        return $this->ceiling($final , 10 );
    }


    function ceiling($number, $significance = 1)
    {
        return round($number/$significance ) * $significance ;
    }


    public function getCoverImageAttribute()
    {
        $final = null ;
        if(count($this->images)) {
            $final =  $this->images[0]->full_url_image;
        } else {
            $final =  asset('assets/admin/images/product-placeholder.jpg');
        }
        return $final;
    }

    public function getDescEscapedAttribute()
    {
        return strip_tags($this->desc);
    }

    public function getVisibleSkuAttribute()
    {
        $finalSku = null;
        if($this->parent == null) {
            $finalSku = 'SKU-'.$this->sku;
        } else {
            $finalSku = 'SKU-'.$this->parent.'-'.$this->sku;
        }
        return $finalSku;
    }

    public function getSubProductCountAttribute()
    {
        return $this->where('parent' , $this->sku)->notFake()->published()->count();
    }

    public function getSubsAttribute()
    {
        return $this->where('parent' , $this->sku)->notFake()->published()->get();
    }

    public function getPlaceholderAttribute()
    {
        return asset('assets/admin/images/product-placeholder.jpg');
    }

    public function getGalleryAttribute()
    {
        return $this->images;
    }

    public function getGalleryCountAttribute()
    {
        return $this->images->count();
    }

    public function getRouteKeyName()
    {
        return 'sku';
    }

    public function scopeParents($query)
    {
        return $query->where('parent', null);
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', 'Y');
    }

    public function scopeNotFake($query)
    {
        return $query->where('fake', '!=' , 'Y');
    }

    public function scopeNotRaw($query)
    {
        return $query->where('type', '!=' , PRODUCT_TYPE_RAW_MATERIAL);
    }

    public function scopeOnlyRaw($query)
    {
        return $query->where('type', '=' , PRODUCT_TYPE_RAW_MATERIAL);
    }

    public function category()
    {
        return $this->belongsTo( ProductCategory::class , 'category_id' ,'id');
    }

    public function otherCategories()
    {
        return $this->belongsToMany( ProductCategory::class , 'product_other_product_category' , 'product_id' , 'category_id' );
    }

    public function manufacture()
    {
        return $this->belongsTo( Manufacture::class , 'manufacture_id' ,'id');
    }

    public function images()
    {
        return $this->hasMany( Images::class );
    }

    public function children()
    {
        return $this->hasMany(Product::class ,  'parent' , 'sku' );
    }

    public function parentProduct()
    {
        return $this->belongsTo(Product::class ,'parent' , 'sku');
    }

    public function age()
    {
        return $this->hasOne(Age::class );
    }

    public function op()
    {
        return $this->hasMany(ProductOptionValue::class );
    }

    public function bom(){
        return $this->hasMany(Bom::class , 'product_id' , 'id');
    }

    public function mainUnit()
    {
        return $this->belongsTo(Unit::class , 'mainUnit_id' , 'id');
    }

    public function subUnit()
    {
        return $this->belongsTo(Unit::class , 'subUnit_id' , 'id');
    }

    public function purchaseGoods()
    {
        return $this->hasMany( Goods::class , 'product_id' , 'id');
    }

    public function saleGoods()
    {
        return $this->hasMany( saleGoods::class , 'product_id' , 'id');
    }

    public function getPriceAttribute()
    {
        $rate = 1;
        $settings = OilSettings::get(['site']);
        if ($settings && $settings->dirhamRate) {
            $rate = $settings->dirhamRate->value;
        }

        $data = $this->latestPrice();
        $final =  round($data['price'] * $rate,null,PHP_ROUND_HALF_UP );
        return $final;
    }

    public function latestPrice()
    {
        return $this->hasMany( PriceListItem::class , 'product_id' , 'id')
            ->with('priceList')
            ->whereHas('PriceList' , function ($q) {
                $q->where(function ($qq) {
                    $qq->where('price_list.created_at',
                        DB::raw('(select max(oo.created_at) as oocreated_at from price_list oo having oocreated_at = price_list.created_at)'));
                });
            })
            ->first();
    }

    public function wareHouseOutGoItems()
    {
        return $this->hasMany( WGoods::class , 'product_id' , 'id');
    }


}