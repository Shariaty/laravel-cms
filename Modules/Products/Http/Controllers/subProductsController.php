<?php

namespace Modules\Products\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Products\AttributeValue;
use Modules\Products\Bom;
use Modules\Products\Product;
use Modules\Products\Unit;

class subProductsController extends Controller
{
    public function subProductsList( Product $product)
    {
        if ($product->category && (count($product->category->attributes) == 0)) {
            return redirect(route('admin.products.list'))->with('warning' , LBL_RESTRICT_ADDING_SUB_PRODUCT);
        }

        $attributeValuesList = AttributeValue::pluck('title' , 'id');
        $subProducts = Product::where('parent' , $product->sku)->notFake()->orderBy('created_at' , 'ASC')->paginate(10);
        return view('products::subProducts.list' , compact('subProducts' , 'product' , 'attributeValuesList'))->with('title' , 'Child products of :'.$product->title);
    }

    public function add($parentSku)
    {
        $test = new ProductsController();
        $test->removeFakeOnes();
        $rand = mt_rand(1111 , 9999);
        $product = Product::create(array('sku' => $rand , 'parent' => $parentSku ,'fake' => 'Y'));
        return redirect(route('admin.subProduct.edit' , $product->sku));
    }

    public function edit(Product $product)
    {
        $rawP = Product::onlyRaw()->get();
        $rawProductsList = $rawP->pluck('title' , 'id');

        $rawProductsListForJs = [ (object)['id' => '' , 'text' => '']];
        foreach ($rawP as $test) {
            $rawProductsListForJs[] = (object)['id' => $test->id, 'text' => $test->title];
        }

        $unitList = Unit::pluck('title' , 'id');
        $mother = Product::whereSku($product->parent)->with('category.attributes.values')->first();
        $motherType = $mother->type;

        $selectedRaw = [];
        $selectedValue = [];

        $filteringData = [];
        if($mother->category) {
            if(count($mother->category->attributes)) {
                    foreach ( $mother->category->attributes as $att) {

                        $values = [];
                        $select2Values =[];
                        if(count($att->values)){
                            foreach ($att->values as $val) {
                                $values[] =  (object) ['id' => $val->id , 'title' => $val->title];
                                $select2Values[$val->id] =  $val->title;
                            }
                        }

                        $filteringData[] = (object)
                            [ 'id' => $att->id ,
                              'title' => $att->title ,
                              'values' => (object) $values ,
                              'select2Values' => $select2Values
                            ];
                    }
                }
        }
        $filteringData = (object) $filteringData;

        if($product->bom && count($product->bom)){
            foreach ($product->bom as $bom) {
                $selectedRaw[] = $bom->rawProduct_id;
                $selectedValue[] = $bom->value;
                $units[] = $bom->rawProduct->mainUnit->title ;
            }
        } else {
            $units[] = null ;
        }

        return view('products::subProducts.edit' , compact('product' ,
            'filteringData'  , 'rawProductsList' , 'rawProductsListForJs' , 'unitList' ,
            'selectedRaw' , 'selectedValue' , 'units' , 'motherType'
        ))->with('title' , 'Edit Product : '.$product->visible_sku);
    }

    public function update(Request $request , Product $product)
    {
        if($request->has('is_published')){
            $request->merge(array('is_published' => 'Y'));
        } else {
            $request->merge(array('is_published' => 'N'));
        }

        if($request->has('price')){
            $converted = intval(str_replace(',', '', $request->input('price')));
            $request->merge(array('price' => $converted));
        }

        $request->merge(array('fake' => 'N'));

        $convert = $request->input('convert');
        $bom = [];
        if ($request->has('rawProduct')) {
            if (count($request->input('rawProduct'))) {
                foreach ($request->input('rawProduct') as $key => $value)
                {
                    $bom[] = new Bom ([
                        'rawProduct_id' => $value ,
                        'sale_invoice_id' => $product->id ,
                        'value' => isset($convert[$key]) ? array_first($convert[$key]) : 0 ,
                    ]);
                }
            }
        }

        $data = $request->except(['_token']);

        DB::transaction( function () use ( $product , $data , $bom ) {
            $product->bom()->forceDelete();
            $product->update($data);
            $product->bom()->saveMany($bom);
        });

        return redirect(route('admin.subProducts.list' , $product->parent))->with('success' , 'success');
    }

    public function delete($id , Request $request)
    {
        $product = Product::whereId($id)->first();

        if($product->forceDelete()){
            $request->session()->flash('success', trans('notify.DELETE_SUCCESS_NOTIFICATION'));
            } else {
            $request->session()->flash('error', trans('notify.DELETE_FAILED_NOTIFICATION'));
        }

        return redirect(route('admin.subProducts.list' , $product->parent));
    }

    public function ajaxGetBomUnit(Request $request)
    {
        $id = $request->input('id');
        $product = Product::whereId($id)->first();

        if ($product) {
            $unit = $product->mainUnit;
            return response()->json([ 'status' => 'success' , 'unit' => $unit ]);
        }
        return response()->json([ 'status' => 'error' , 'message' => LBL_COMMON_ERROR ]);
    }

}
