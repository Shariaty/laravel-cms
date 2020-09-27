<?php

namespace Modules\Portfolio\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Portfolio\Portfolio;
use Modules\Portfolio\PortfolioCategory;
use Modules\Products\AttributeValue;
use Modules\Products\Option;
use Modules\Products\Product;


class ProductsApiController extends Controller
{

    protected $attributeValuesList;
    protected $attributeValuesIconList;

    public function __construct()
    {
        $this->attributeValuesList = AttributeValue::pluck('title' , 'id');
        $this->attributeValuesIconList = AttributeValue::get()->pluck('full_icon' , 'id');
    }

    public function getCategories(PortfolioCategory $parentCat)
    {
        $categories = null;

        if ($parentCat->id) {
            $categories = PortfolioCategory::where('parent' , $parentCat->id)->published()->get();
        } else {
            $categories = PortfolioCategory::where('parent' , 0)->published()->get();
        }

        return response()->json(['categories' => $categories ]);
    }

    public function getAllProduct()
    {
        $products = Product::parents()->published()->notfake()->with('category')->paginate(12);
        return response()->json(['products' => $products ]);
    }

    public function getSearchedProduct(Request $request)
    {
        $response = null;
        $attributes = null;

        $builder = Portfolio::query();
        $builder->select(['*'])->published()->notfake();

        $categorySearchIds = [];


        if ($request->has('secondCategory')) {
            $secondSlug = $request->input('secondCategory');

            $builder->whereHas('category' , function ($query) use ($secondSlug) {
                $query->where('slug' , $secondSlug);
            });
        }

//        if ($request->has('category') && $request->has('secondCategory')) {
//            $slug = $request->input('category');
//            $secondSlug = $request->input('secondCategory');
//
//            //create array of all child category ids
//            $category = PortfolioCategory::where('slug' , $slug)->first();
//            if($category) {
//                if(count($category->children)) {
//                    foreach ($category->children as $child) {
//                        if(count($child->children)) {
//                            foreach ($child->children as $secondChild) {
//                                $categorySearchIds[] = $secondChild->id;
//                            }
//                            $categorySearchIds[] = $child->id ;
//                        } else {
//                            $categorySearchIds[] = $child->id;
//                        }
//                    }
//                    $categorySearchIds[] = $category->id ;
//                } else {
//                    $categorySearchIds[] = $category->id ;
//                }
//            } else {
//                $categorySearchIds = [] ;
//            }
//            $categorySearchIds = stringArrayConvertToIntArray($categorySearchIds);
//            //create array of all child category ids
//
//            $builder->whereHas('category' , function ($query) use ($categorySearchIds) {
//                $query->whereIn('id' , $categorySearchIds);
//            });
//        }

//        if ($request->has('manufactures')) {
//            $manufactures = stringArrayConvertToIntArray($request->input('manufactures'));
//            $builder->whereHas('manufacture' , function ($query) use ($manufactures) {
//                $query->whereIn('id' , $manufactures);
//            });
//        }

//        if ($request->has('q')){
//            $builder->where('title', 'like', '%' .$request->input('q'). '%');
//        }


        if ($request->has('sort') ) {
            switch ( $request->input('sort') ) {
                case "10" :
                    $builder->orderBy('created_at' ,  'DESC' );
                break;
                case "20" :
                    $builder->orderBy('created_at' ,  'ASC' );
                break;
                case "30" :
                    $builder->orderBy('finalPrice' ,  'ASC' );
                break;
                case "40" :
                    $builder->orderBy('finalPrice' ,  'DESC' );
                break;
                default:
                    $builder->orderBy('created_at' ,  'DESC' );
                break;
            }
        } else {
            $builder->orderBy('created_at' ,  'DESC' );
        }

        $builder->with('category');
        $response = $builder->orderBy('created_at' , 'asc')->paginate(24);

        return response()->json([
            'products' => $response ,
            'ids' => $attributes ,
            'cat' => $request->has('secondCategory') ?  $request->input('secondCategory') : 'unknown' ,
            'catIds' => $categorySearchIds ,
            'com' =>  stringArrayConvertToIntArray($request->input('combination'))
           ]);
    }
    
    public function getSpecificProduct($sku)
    {
        $product = Portfolio::whereSku($sku)->with('category')->with('designer')->first();


        return response()->json([
            'product' => $product
        ]);
    }

    protected function generateSubs($subs)
    {
        $data = [];
        foreach ($subs as $sub) {
            $data[$sub->id] = [
                'id' => $sub->id ,
                'slug' => $sub->slug ,
                'title' => $sub->title ,
                'subs' => count($sub->published_children) ? $this->generateSubs($sub->published_children) : null
            ];
        }
        return  $data;
    }

    public function generateStockForComplexProduct($product)
    {

        $finalStock = 0;
        if (count($product->bom)) {

            $ids= [];
            foreach ($product->bom as $bom) {
                $ids[] = $bom->rawProduct_id;
            }

            $rawProducts = Product::whereIn('products.id' , $ids)->with(['purchaseGoods' , 'wareHouseOutGoItems'])->get();

            $purchaseArray = [];
            $outGoArray = [];
            $mojoodiArray = [];

            foreach ($rawProducts as $p){
                $tedadKharid = $p->purchaseGoods->sum('quantity');
                $tedadForosh = $p->wareHouseOutGoItems->sum('quantity');
                $mojoodiArray[] = $tedadKharid - $tedadForosh ;
                $purchaseArray[$p->title] = $tedadKharid;
                $outGoArray[$p->title] = $tedadForosh;
            }
            $finalStock = min($mojoodiArray) ;
        }


        $final = [
            'finalStock' => $finalStock ,
            'detailedVersion' => [
                'purchaseArray' =>  $purchaseArray,
                'WareHouseOutGo' =>  $outGoArray,
                'mojoodiArray' => $mojoodiArray
            ]
        ];

        return $final;


//        $tedadForosh = $product->saleGoods->sum('final_quantity');
//
//        $rawProducts = [];
//        $saleResults = [];
//        $purchaseResults = [];
//
//        foreach ($product->bom as $bom) {
//            $rawProducts[] = $bom->rawProduct;
//            $saleResults[] = ['finalSale' => $bom->value * $tedadForosh , 'val' => $bom->value];
//        }
//
//        foreach ($rawProducts as $p) {
//            $tedadKharid = $p->purchaseGoods->sum('quantity');
//            $purchaseResults[] = ['finalPurchase' => $tedadKharid ];
//        }
//
//        $detailed= [];
//        $canMade = [];
//
//        foreach ($saleResults as $key => $value) {
//            $detailed[] =  [
//                'quantity' => $purchaseResults[$key]['finalPurchase'] - $value['finalSale'],
//                'canMade' => ($purchaseResults[$key]['finalPurchase'] - $value['finalSale']) / $value['val'],
//                'unit' => $value['val'],
//            ];
//            $canMade[] = ($purchaseResults[$key]['finalPurchase'] - $value['finalSale']) / $value['val'];
//        }
//
//        $finalStock = min($canMade);
//        $final = [  'finalStock' => $finalStock ,
//                    'detailedVersion' => [
//                        $tedadForosh ,
//                        $saleResults ,
//                        $purchaseResults ,
//                        $detailed ,
//                        $finalStock] ,
//        ];
//
    }

    public function getFinderData()
    {
        $data = Option::with('values')->get();

        $final = [];
        foreach ($data as $d){
            $res = [];
            foreach ($d->values as $value) {
                $res[$value->id] = $value->title ;
            }

            $final[$d->e_title] = $res;

        }
        return response()->json( $final );
    }
}
