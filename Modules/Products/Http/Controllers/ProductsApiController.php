<?php

namespace Modules\Products\Http\Controllers;

use App\Http\Controllers\Controller;
use function foo\func;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Products\AttributeValue;
use Modules\Products\Option;
use Modules\Products\Product;
use Modules\Products\ProductCategory;


class ProductsApiController extends Controller
{

    protected $attributeValuesList;
    protected $attributeValuesIconList;

    public function __construct()
    {
        $this->attributeValuesList = AttributeValue::pluck('title' , 'id');
        $this->attributeValuesIconList = AttributeValue::get()->pluck('full_icon' , 'id');
    }

    public function getCategories()
    {
        $categories = ProductCategory::parents()->published()->where('raw_material' , 'N')->get();
        return response()->json(['categories' => $categories ]);
    }

    public function getAllProduct()
    {
        $products = Product::parents()->published()->notfake()->with('category')->paginate(12);
        return response()->json(['products' => $products ]);
    }

    public function getSearchedProduct(Request $request)
    {
        $qexpression = '(
                select
                        CASE
                         WHEN
                           products.price is not null
                             THEN
                               products.price
                         WHEN
                           ps.price is null
                             THEN
                               0
                         WHEN
                           products.price is null
                             THEN
                               min(ps.price)
                         ELSE
                           0
                        END AS price
                
                    from `products` as `ps` where  `ps`.`parent` = `products`.`sku` limit 1
                
                ) as `finalPrice`' ;

        $response = null;
        $attributes = null;

        $builder = Product::query();
//        $builder->select(['*' ,  DB::raw( $qexpression )])->published()->notfake();
        $builder->select(['*'])->where('type' , '!=' , PRODUCT_TYPE_RAW_MATERIAL)
            ->published()->notfake();

        $categorySearchIds = [];

        if($request->has('category')) {
            $slug = $request->input('category');

            //create array of all child category ids
            $category = ProductCategory::where('slug' , $slug)->first();
            if($category) {
                if(count($category->children)) {
                    foreach ($category->children as $child) {
                        if(count($child->children)) {
                            foreach ($child->children as $secondChild) {
                                $categorySearchIds[] = $secondChild->id;
                            }
                            $categorySearchIds[] = $child->id ;
                        } else {
                            $categorySearchIds[] = $child->id;
                        }
                    }
                    $categorySearchIds[] = $category->id ;
                } else {
                    $categorySearchIds[] = $category->id ;
                }
            } else {
                $categorySearchIds = [] ;
            }
            $categorySearchIds = stringArrayConvertToIntArray($categorySearchIds);
            //create array of all child category ids

            $builder->whereHas('category' , function ($query) use ($categorySearchIds) {
                $query->whereIn('id' , $categorySearchIds);
            });

            $builder->orWhereHas('otherCategories' , function ($query) use ($categorySearchIds) {
                $query->whereIn('product_other_product_category.category_id' , $categorySearchIds);
            });
        }

        if($request->has('attribute')) {
            $attributes = stringArrayConvertToIntArray($request->input('attribute'));

            foreach ($attributes as $attribute) {
                $builder->whereHas('op', function ($query) use ($attribute) {
                    $query->where('products_products_options_values.value_id','=',$attribute);
                });
            }
        }

        if($request->has('combination')) {

            $combination = stringArrayConvertToIntArray($request->input('combination'));

            foreach ($combination as $com) {
                $builder->whereHas('children', function ($query) use ($com) {
                    $query->where('option_1' , $com);
                    $query->orWhere('option_2' , $com);
                });
            }

//            $builder->whereHas('children' , function ($query) use ( $combination ){
//                $query->whereIn('option_1' , $combination);
//                $query->whereIn('option_2' , $combination , 'OR');
//            });

        }

        if($request->has('manufactures')) {
            $manufactures = stringArrayConvertToIntArray($request->input('manufactures'));

            $builder->whereHas('manufacture' , function ($query) use ($manufactures) {
                $query->whereIn('id' , $manufactures);
            });
        }


        if ( $request->has('sort') ) {
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

        if($request->has('q')){
            $builder->where('title', 'like', '%' .$request->input('q'). '%');
        }

        $builder->with('category');
        $response = $builder->where('parent' , null)->orderBy('created_at' , 'asc')->paginate(24);
        return response()->json([
            'products' => $response ,
            'ids' => $attributes ,
            'cat' => $request->input('category') ,
            'catIds' => $categorySearchIds ,
            'com' =>  stringArrayConvertToIntArray($request->input('combination'))
           ]);
    }
    
    public function getSpecificProduct($sku)
    {
        $product = Product::whereSku($sku)->with('category.attributes')->with(['subUnit' , 'mainUnit'])->first();

        $option = [];
        $tags = [];
        if($product->sub_product_count) {
            foreach ($product->subs as $sub) {
                $option[$sub->option_1] =  $this->attributeValuesList[$sub->option_1] ;
            }
        }

        $fullOptions = [];
        if($product->sub_product_count) {
            foreach ($product->subs as $sub) {
                $fullOptions[$sub->option_1] =  [ 'title' => $this->attributeValuesList[$sub->option_1] , 'icon' => $this->attributeValuesIconList[$sub->option_1]] ;
            }
        }

        $subject = [ 'not found' => 'یافت نشد'];
        if(count($product->category->attributes)){
            $subject = [ $product->category->attributes[0]->e_title => $product->category->attributes[0]->title];
        }
        $title = $subject ;

        if($product->op) {
            $tags = $product->op;
        }

        return response()->json([
            'product' => $product ,
            'options' => $option ,
            'firstLevelTitle' => $title ,
            'full' => $fullOptions ,
            'tags' => $tags
        ]);
    }

    public function getCombination(Request $request)
    {
        $sku = $request->input('sku');
        $id = $request->input('id');

        $parentProduct = Product::whereSku($sku)->first();
        $products = Product::whereParent($sku)->where('option_1' , $id)->get();

        $data= [];

        foreach ($products as $product) {
            if(isset($product->option_2) && !empty($product->option_2))
                $data[$product->option_2] = $this->attributeValuesList[$product->option_2];
        }

        $fullData = [];
        foreach ($products as $product) {
            if(isset($product->option_2) && !empty($product->option_2))
                $fullData[$product->option_2] = ['title' => $this->attributeValuesList[$product->option_2] , 'icon' => $this->attributeValuesIconList[$product->option_2]];
        }


        $subject = [ 'not found' => 'یافت نشد'];
        if(count($parentProduct->category->attributes) > 1){
            $subject = [ $parentProduct->category->attributes[1]->e_title => $parentProduct->category->attributes[1]->title];
        }

        $title = $subject ;

        return response()->json(['sku' => $sku , 'id' => $id , 'res' => $fullData , 'subject' => $title]);
    }

    public function calculatePrice(Request $request)
    {
        $ids = $request->input('ids');
        $sku = $request->input('sku');
        $response = [];
        switch (count($ids)) {
            case 1 :
                if($ids[0] == null) {
                    $product = Product::whereSku($sku)->first();
                } else {
                    $product = Product::whereParent($sku)->where('option_1' , $ids[0])->first();
                }
            break;
            case 2 :
                $product = Product::whereParent($sku)
                    ->where('option_1' , $ids[0])
                    ->where('option_2' , $ids[1])->first();
            break;
            default:
                $product = null ;
        }

        $response = $product;

        $mojoodi = 0;
        $DetailetMojoodi= null;

        if($product->parent) {
            if ( $product->parentProduct->type === PRODUCT_TYPE_COMPLEX ) {
//            if ( count($product->parentProduct->bom) ) {
                $result = $this->generateStockForComplexProduct($product);
                $mojoodi = $result['finalStock'];
                $DetailetMojoodi = $result['detailedVersion'];
            } else {
                $tedadKharid = $product->purchaseGoods->sum('quantity');
                $tedadForosh = $product->wareHouseOutGoItems->sum('quantity');
                $mojoodi = $tedadKharid - $tedadForosh;
            }
        } else {
            $tedadKharid = $product->purchaseGoods->sum('quantity');
            $tedadForosh = $product->wareHouseOutGoItems->sum('quantity');
            $mojoodi = $tedadKharid - $tedadForosh;
        }

        return response()->json([
            'data' => $response ,
            'sendedIds' => $ids[0] == null ,
            'mojooodi'  => $mojoodi,
            'detailed' => $DetailetMojoodi
        ]);
    }

    public function getFilters(Request $request){

        $final = null;
        $attributes = null;
        $manufactures = null ;
        $RecievedCatId = null ;
        $finalCategoryList = [];

        if( $request->has('category') && !empty($request->input('category')) ){

            $selectedCategory = ProductCategory::where('slug' , $request->input('category'))->published()
                ->with('options')->with('attributes')->first();

            $RecievedCatId = $selectedCategory->id;

            $categories = [
                'categories' => [$selectedCategory] ,
                'all' => false
            ];

            if ( $selectedCategory->parent == 0 ) {
                    $finalCategoryList[$selectedCategory->id] = [
                        'id' => $selectedCategory->id ,
                        'slug' => $selectedCategory->slug ,
                        'title' => $selectedCategory->title ,
                        'subs' =>  $this->generateSubs($selectedCategory->published_children),
                        'appended' => false
                    ];

            } else {
                $cats = ProductCategory::whereId($RecievedCatId)->published()->where('raw_material' , 'N')->first();

                $finalCategoryList[$cats->id] = [
                    'id' => $cats->id,
                    'slug' => $cats->slug,
                    'title' => $cats->title,
                    'subs' => $this->generateSubs($cats->published_children),
                    'appended' => false
                ];

                $fatherCategory = ProductCategory::whereId($cats->parent)->published()->where('raw_material' , 'N')->first();
                $finalCategoryList[$fatherCategory->id] = [
                    'id' => $fatherCategory->id,
                    'slug' => $fatherCategory->slug,
                    'title' => $fatherCategory->title,
                    'subs' => null,
                    'appended' => true
                ];
            }

        } else {
            $categories = [
                'categories' => ProductCategory::published()->with('options')->get() ,
                'all' => true
            ];

            $cats = ProductCategory::parents()->published()->where('raw_material' , 'N')->get();
            foreach ($cats as $cat) {
                $finalCategoryList[$cat->id] = [
                    'id' => $cat->id ,
                    'slug' => $cat->slug ,
                    'title' => $cat->title ,
                    'subs' =>  $this->generateSubs($cat->published_children)
                ];
            }
        }

        if(count($categories['categories'])) {
            foreach ($categories['categories'] as $category) {
                if(count($category->options)) {
                    foreach ($category->options as $option) {

                        $final[ $option->id ] = [ "text" => $option->title ];
                        if(count($option->values)) {
                            foreach ($option->values as $val) {
                                $final[$option->id]["children"][] = [ "id" => $val->id , "text" => $val->title];
                            }
                        }
                    }
                }

                if(count($category->attributes)) {
                    foreach ($category->attributes as $attribute) {

                        $attributes[ $attribute->id ] = [ "text" => $attribute->title ];
                        if(count($attribute->values)) {
                            foreach ($attribute->values as $val) {
                                $attributes[$attribute->id]["children"][] = [ "id" => $val->id , "text" => $val->title];
                            }
                        }
                    }
                }

                if(count($category->manufactures)) {
                    foreach ($category->manufactures as $manufacture) {
                        $manufactures[$manufacture->id] = [ "id" => $manufacture->id , "text" => $manufacture->title , 'e_text' => $manufacture->e_title];
                    }
                }
            }
        }

        return response()->json([
            'filters' => $final ,
            'attributes' => $attributes ,
            'manufactures' => $manufactures ,
            'allCategories' => count($finalCategoryList) ? $finalCategoryList : null ,
            'cateId' => $RecievedCatId
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

    public function wizardSearch(Request $request)
    {
        $type = $request->input('type');
        $age = $request->input('age');

        $purity = $request->input('purity');
        $sterilization = $request->input('sterilization');
        $sens = $request->input('sens');

        $dogState = $request->input('dogState');
        $race = $request->input('race');


        $builder = Product::query();
        $builder->select(['*'])->where('type' , '!=' , PRODUCT_TYPE_RAW_MATERIAL)->published()->notfake();


        $builder->whereHas('age', function ($query) use ($age) {
            $query->where('products_agerange.start','<=',$age);
            $query->where('products_agerange.end','>=',$age);
        });

        $builder->whereHas('op', function ($query) use ($type , $sens , $sterilization) {
            $query->where('products_products_options_values.value_id','=',$type);
        });

        if ($purity == 'yes') {
            // Check the race
            if ($race) {
                $builder->whereHas('op', function ($query) use ($race) {
                    $query->where('products_products_options_values.value_id','=',$race);
                });
            }
        } else if ($purity == 'no' && $type == '2') {
            // Check the dog size
            if ($dogState) {
                $builder->whereHas('op', function ($query) use ($dogState) {
                    $query->where('products_products_options_values.value_id','=',$dogState);
                });
            }
        }


        if ($sterilization) {
            $builder->whereHas('op', function ($query) {
                $query->orWhere('products_products_options_values.value_id','=',500);
            });
        }


        $response = $builder->orderBy('created_at' , 'asc')->get();

        return response()->json(['products' => $response]);
    }
}
