<?php

namespace Modules\Sale\Http\Controllers;

use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\Products\AttributeValue;
use Modules\Products\Product;
use Modules\Products\Unit;
use Modules\Sale\SaleInvoice;
use Modules\Sale\Goods;
use Modules\Warehouse\WGoods;
use Yajra\Datatables\Datatables;
use Opilo\Farsi\JalaliDate;



class SaleController extends Controller
{
    protected  $redirectPath = 'administrator/sale/list';

    protected $rules = array(
        'title'      => 'required|max:100'
    );

    public function index()
    {
        return view('sale::list')->with('title' , 'Sale Invoice');
    }
    // -------------------------------------------------------------------------------
    public function anyData()
    {
        return Datatables::of(SaleInvoice::all())
            ->editColumn('created_at', function ($item) {
                return $item->created_at->format('Y/m/d');
            })
            ->editColumn('total_price' , function ($item) {
                return '<span class="price">'.$item->total_price.'</span>';
            })
            ->editColumn('customer' , function ($item) {
                return '<span>'.$item->user->fullName.'</span>';
            })
            ->addColumn('action' , function ($item) {
                return $this->render($item);
            })
            ->rawColumns(['is_published', 'total_price' , 'action' , 'customer' , 'created_at'])
            ->make(true);
    }
    // -------------------------------------------------------------------------------
    public function render( $item ) {
        $final = '';

        if ( $item->type !== SITE_ORDER ) {
            $final .= '<a href="'. route('admin.sale.edit' , $item->id).'" class="btn btn-xs btn-warning"><i class="fa fa-edit"></i></a>';
        }
        $final .= '<a data-id="'.$item->id.'" class="btn btn-xs red delete_btn"><i class="fa fa-trash"></i></a>';
        $final .= '<a href="'.route('admin.order.detail' , $item).'" class="btn btn-xs btn-info"><i class="fa fa-search"/></a>';
        return $final;
    }
    // -------------------------------------------------------------------------------
    public function add()
    {
        $products = Product::notFake()->notRaw()->get();
        $attributes = AttributeValue::pluck('title' , 'id');

        $productListForJs = [ (object)['id' => '' , 'text' => '']];

        foreach ($products as $product) {
            if(!$product->parent && $product->sub_product_count == 0) {
                $productListForJs[] = (object)[ 'id' => $product->id , 'text' => $product->title ];
            }

            if(!$product->parent && $product->sub_product_count > 0) {
                foreach ($product->subs as $subProduct) {
                    $productListForJs[] = (object)[ 'id' => $subProduct->id , 'text' => $this->generate($subProduct , $product , $attributes)];
                }
            }
        }

        $unitList = [ (object)['id' => '' , 'text' => '']];
        $units = Unit::all();
        foreach ($units as $unit) {
            $unitList[] = (object)[ 'id' => $unit->id , 'text' => $unit->title ];
        }

        $users = User::pluck('fullName' , 'id')->toArray();
        $usersList = array_add($users , '' , '');
        return view('sale::add' , compact('productListForJs' , 'unitList' , 'usersList'))->with('title' , 'Purchase Invoice add/edit');
    }
    // -------------------------------------------------------------------------------
    public function create(Request $request)
    {
        $rand = $this->generateFactorNumber();
        $invoice = SaleInvoice::create(
            [
                'type' => OPERATOR_ORDER ,
                'title' => $request->input('title'),
                'user_id' => $request->input('user'),
                'total_price' => $request->input('total_price'),
                'total_qty' => $request->input('total_qty'),
                'invoice_number' => $rand,
                'discount' => $request->input('discount'),
                'shipping' => $request->input('shipping')
            ]);

        $prices = $request->input('price');
        $quantities = $request->input('qty');
        $units = $request->input('unit');
        $cons = $request->input('con');

        $data= [];
        if(count($request->input('item')) > 0){
            foreach ($request->input('item') as $key => $value) {
                $data[] = new Goods ([
                    'product_id' => $value ,
                    'sale_invoice_id' => $invoice->id ,
                    'sale_price' => isset($prices[$key]) ? array_first($prices[$key]) : 0 ,
                    'quantity' => isset($quantities[$key]) ? array_first($quantities[$key]) : 0 ,
                    'unit_id' => isset($units[$key]) ? array_first($units[$key]) : null ,
                    'final_quantity' => isset($cons[$key]) && isset($quantities[$key])  ? array_first($cons[$key]) * array_first($quantities[$key]) : 0 ,
                ]);
            }
        }
        $invoice->goods()->saveMany($data);


        $wareHouseOutGo = $invoice->wareHouseOutGo()->create(['code' => $rand]);
        $Wdata = [];
        if(count($request->input('item')) > 0){
            foreach ($request->input('item') as $key => $value) {

                $product = Product::whereId($value)->with(['bom.rawProduct' , 'parentProduct'])->first();
                if (count($product->bom)) {
                    foreach ($product->bom as $bom) {
                        $unit = isset($cons[$key]) && isset($quantities[$key])  ? array_first($cons[$key]) * array_first($quantities[$key]) : 0;
                        $Wdata[] = new WGoods ([
                            'product_id' => $bom->rawProduct_id ,
                            'unit_id' => $bom->rawProduct->mainUnit_id,
                            'warehouse_outgo_id' => $wareHouseOutGo->id ,
                            'quantity' => $unit * $bom->value
                        ]);
                    }
                } else {
                    $Wdata[] = new WGoods ([
                        'product_id' => $value ,
                        'unit_id' => $product->parentProduct ? $product->parentProduct->mainUnit_id : $product->mainUnit_id ,
                        'warehouse_outgo_id' => $wareHouseOutGo->id ,
                        'quantity' => isset($cons[$key]) && isset($quantities[$key])  ? array_first($cons[$key]) * array_first($quantities[$key]) : 0
                    ]);
                }
            }
        }
        $wareHouseOutGo->wGoods()->saveMany($Wdata);

        return redirect(route('admin.sale.list'))->with('success' , 'Invoice has been created');
    }
    // -------------------------------------------------------------------------------
    public function edit(SaleInvoice $invoice) {
        $invoiceGoods = $invoice->goods;

        $attributes = AttributeValue::pluck('title' , 'id');
        $products = Product::notFake()->get();

        $productList = [];
        foreach ($products as $product) {
            if(!$product->parent && $product->sub_product_count == 0) {
                $productList[$product->id] =  $product->title;
            }

            if(!$product->parent && $product->sub_product_count > 0) {
                foreach ($product->subs as $subProduct) {
                    $productList[$subProduct->id] = $this->generate($subProduct , $product , $attributes) ;
                }
            }
        }

        $productListForJs = [];
        foreach ($productList as $key => $value) {
            $productListForJs[] = (object)[ 'id' => $key , 'text' => $value ];
        }

        $users = User::pluck('fullName' , 'id')->toArray();
        $usersList = array_add($users , '' , '');

        return view('sale::edit' , compact('invoice' , 'productListForJs' ,
            'invoiceGoods' , 'productList' , 'usersList'))->with('title' , 'Sale Invoice edit :'.$invoice->title);
    }
    // -------------------------------------------------------------------------------
    public function update(Request $request , SaleInvoice $invoice) {

        $title = $request->input('title');
        $user_id = $request->input('user');
        $total_price = $request->input('total_price');
        $total_qty = $request->input('total_qty');
        $discount  = $request->input('discount');
        $shipping = $request->input('shipping');
        $prices = $request->input('price');
        $quantities = $request->input('qty');
        $units = $request->input('unit');
        $cons = $request->input('con');

        $data = [];
        if(count($request->input('item')) > 0)
        {
            foreach ($request->input('item') as $key => $value)
            {
                $data[] = new Goods ([
                    'product_id' => $value ,
                    'sale_invoice_id' => $invoice->id ,
                    'sale_price' => isset($prices[$key]) ? array_first($prices[$key]) : 0 ,
                    'quantity' => isset($quantities[$key]) ? array_first($quantities[$key]) : 0 ,
                    'unit_id' => isset($units[$key]) ? array_first($units[$key]) : null ,
                    'final_quantity' => isset($cons[$key]) && isset($quantities[$key])  ? array_first($cons[$key]) * array_first($quantities[$key]) : 0 ,
                ]);
            }
        }

        $result = DB::transaction( function () use ($invoice , $data , $title , $user_id , $total_price , $total_qty , $discount , $shipping) {
            if($invoice->goods()->forceDelete()) {
                $invoice->title = $title;
                $invoice->user_id = $user_id;
                $invoice->discount = $discount;
                $invoice->shipping = $shipping;
                $invoice->total_price = $total_price;
                $invoice->total_qty = $total_qty;
                $invoice->save();
                $invoice->goods()->saveMany($data);
                return true;
            }
            return false;
        });

        if ($result)
            return redirect(route('admin.sale.list'))->with('success' , LBL_COMMON_UPDATE_SUCCESSFUL);

        return redirect(route('admin.sale.list'))->with('error' , LBL_COMMON_ERROR);

    }
    // -------------------------------------------------------------------------------
    public function delete(SaleInvoice $invoice)
    {

        $result = DB::transaction( function () use ($invoice) {
            if($invoice->delete()) {
                $invoice->goods()->delete();
                return true;
            }
            return false;
        });
        if ($result)
            return response()->json(['status' => 'success', 'message' => LBL_COMMON_DELETE_SUCCESSFUL]);

        return response()->json(['status' => 'error', 'message' => LBL_COMMON_DELETE_ERROR]);
    }
    // -------------------------------------------------------------------------------
    protected function generate($subProduct , $product , $attributes){
        $string = $product->title ;
        if($subProduct->option_1) {
            $string.= '-'.$attributes[$subProduct->option_1];
        }
        if($subProduct->option_2) {
            $string.= '-'.$attributes[$subProduct->option_2];
        }
        if($subProduct->option_3) {
            $string.= '-'.$attributes[$subProduct->option_3];
        }
        return $string;
    }
    // -------------------------------------------------------------------------------
    public function ajaxGetPriceAndQuantity(Request $request)
    {
        $productID = $request->input('id');
        $product = Product::where('id' , $productID)->with(['purchaseGoods' , 'wareHouseOutGoItems' , 'bom'])->first();
        if ($product) {
            $mojoodi = 0;
            $unit = null;


            if(count($product->bom)) {
                $ids= [];
                foreach ($product->bom as $bom) {
                    $ids[] = $bom->rawProduct_id;
                }

                $rawProducts = Product::whereIn('products.id' , $ids)->with(['purchaseGoods' , 'wareHouseOutGoItems'])->get();
                $mojoodiArray = [];
                foreach ($rawProducts as $p){
                    $tedadKharid = $p->purchaseGoods->sum('quantity');
                    $tedadForosh = $p->wareHouseOutGoItems->sum('quantity');
                    $mojoodiArray[] = $tedadKharid - $tedadForosh ;
                }
                $mojoodi = min($mojoodiArray) ;

            } else {
                $tedadKharid = $product->purchaseGoods->sum('quantity');
                $tedadForosh = $product->wareHouseOutGoItems->sum('quantity');
                $mojoodi = $tedadKharid - $tedadForosh ;
            }

            $unitList = [];
            $con = 0 ;
            if($product->parent) {
                $con = $product->parentProduct->conversion_factor;
                $unitList[] = [ 'id' => $product->parentProduct->mainUnit->id , 'text' => $product->parentProduct->mainUnit->title ];
                if($product->parentProduct->subUnit) {
                    $unitList[] = [ 'id' => $product->parentProduct->subUnit->id , 'text' => $product->parentProduct->subUnit->title ];
                }
            } else {
                $con = $product->conversion_factor;
                $unitList[] = [ 'id' => $product->mainUnit->id , 'text' => $product->mainUnit->title ];
                if($product->subUnit) {
                    $unitList[] = [ 'id' => $product->subUnit->id , 'text' => $product->subUnit->title ];
                }
            }

            return response()->json(['status' => 'success' ,
                'response' => [
                    'price' => $product->price ,
                    'stock' => $mojoodi ,
                    'con'   => $con ? $con : 1 ,
                    'unitList'  => $unitList,
                    'data' => $product ,
                    'bom' => count($product->bom) ? $product->bom : null
                ]
            ]);
        }
        return response()->json(['status' => 'error' , 'message' => 'product not found']);

    }
    // -------------------------------------------------------------------------------
    public function generateFactorNumber(){
        $date = JalaliDate::fromDateTime( Carbon::now() )->format('ym' , false );
        $lastInvoice = SaleInvoice::orderBy('created_at', 'desc')->first();
        $rand = '';

        if ($lastInvoice){
            if(substr(($lastInvoice->invoice_number), 0 , 4) != $date) {
                $rand =  intval($date.'0001');
            } else {
                $rand =  $lastInvoice->invoice_number + 1;
            }
        } else {
           $rand =  intval($date.'0001');

        }

        return $rand;
    }
}

