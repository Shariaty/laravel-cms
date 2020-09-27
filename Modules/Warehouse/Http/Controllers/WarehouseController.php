<?php

namespace Modules\Warehouse\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\Products\AttributeValue;
use Modules\Products\Product;
use Modules\Products\Unit;
use Modules\Warehouse\Goods;
use Modules\Warehouse\PurchaseInvoice;
use Yajra\Datatables\Datatables;
use niklasravnsborg\LaravelPdf\Facades\Pdf;
use Opilo\Farsi\JalaliDate;


class WarehouseController extends Controller
{
    protected  $redirectPath = 'administrator/warehouse/list';

    protected $rules = array(
        'title'      => 'required|max:100'
    );

    public function index()
    {
        return view('warehouse::list')->with('title' , 'Purchase Invoice');
    }
    // -------------------------------------------------------------------------------
    public function anyData()
    {
        return Datatables::of(PurchaseInvoice::all())
            ->editColumn('total_price' , function ($item) {
                return '<span class="price">'.$item->total_price.'</span>';
            })
            ->addColumn('action' , function ($item) {
                return $this->render($item);
            })
            ->editColumn('created_at', function ($portal) {
                return $portal->created_at->format('Y/m/d');
            })
            ->rawColumns(['total_price' , 'action' , 'created_at'])
            ->make(true);
    }
    // -------------------------------------------------------------------------------
    public function render( $item ) {
        $final = '';
        $final .= '<a href="'. route('admin.warehouse.edit' , $item->id).'" class="btn btn-xs btn-warning"><i class="fa fa-edit"></i></a>';
        $final .= '<a data-id="'.$item->id.'" class="btn btn-xs red delete_btn"><i class="fa fa-trash"></i></a>';
        return $final;
    }
    // -------------------------------------------------------------------------------
    public function add()
    {
        $products = Product::notFake()->get();
        $attributes = AttributeValue::pluck('title' , 'id');

        $productList = [];

        foreach ($products as $product) {
            if(!$product->parent && $product->sub_product_count == 0) {
                $productList[] = (object)[ 'id' => $product->id , 'text' => $product->title ];
            }

            if(!$product->parent && $product->sub_product_count > 0) {
                foreach ($product->subs as $subProduct) {
                    $productList[] = (object)[ 'id' => $subProduct->id , 'text' => $this->generate($subProduct , $product , $attributes)];
                }
            }
        }

        return view('warehouse::add' , compact('productList'))->with('title' , 'Purchase Invoice add/edit');
    }
    // -------------------------------------------------------------------------------
    public function create(Request $request)
    {
        $rand = mt_rand(111111 , 999999);
        $invoice = PurchaseInvoice::create(
            [
                'title' => $request->input('title'),
                'total_price' => $request->input('total_price'),
                'total_qty' => $request->input('total_qty'),
                'invoice_number' => $rand
            ]);

        $prices = $request->input('price');
        $quantities = $request->input('qty');

        $data= [];
        if(count($request->input('item')) > 0){
            foreach ($request->input('item') as $key => $value) {
                $data[] = new Goods ([
                    'product_id' => $value ,
                    'purchase_invoice_id' => $invoice->id ,
                    'purchase_price' => isset($prices[$key]) ? array_first($prices[$key]) : 0 ,
                    'quantity' => isset($quantities[$key]) ? array_first($quantities[$key]) : 0 ,
                ]);
            }
        }

        $invoice->goods()->saveMany($data);

        return redirect(route('admin.warehouse.list'))->with('success' , 'Invoice has been created');
    }
    // -------------------------------------------------------------------------------
    public function edit(PurchaseInvoice $invoice) {
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

        return view('warehouse::edit' , compact('invoice' , 'productListForJs' ,
                                                         'invoiceGoods' , 'productList'))->with('title' , 'Purchase Invoice edit :'.$invoice->title);
    }
    // -------------------------------------------------------------------------------
    public function update(Request $request , PurchaseInvoice $invoice) {

        $title = $request->input('title');
        $total_price = $request->input('total_price');
        $total_qty = $request->input('total_qty');
        $prices = $request->input('price');
        $quantities = $request->input('qty');

        $data = [];
        if(count($request->input('item')) > 0)
        {
            foreach ($request->input('item') as $key => $value)
            {
                $data[] = new Goods ([
                    'product_id' => $value ,
                    'purchase_invoice_id' => $invoice->id ,
                    'purchase_price' => isset($prices[$key]) ? array_first($prices[$key]) : 0 ,
                    'quantity' => isset($quantities[$key]) ? array_first($quantities[$key]) : 0 ,
                ]);
            }
        }

        $result = DB::transaction( function () use ($invoice , $data , $title , $total_price , $total_qty) {
            if($invoice->goods()->forceDelete()) {
                $invoice->title = $title;
                $invoice->total_price = $total_price;
                $invoice->total_qty = $total_qty;
                $invoice->save();
                $invoice->goods()->saveMany($data);
                return true;
            }
            return false;
        });

        if ($result)
            return redirect(route('admin.warehouse.list'))->with('success' , LBL_COMMON_UPDATE_SUCCESSFUL);

        return redirect(route('admin.warehouse.list'))->with('error' , LBL_COMMON_ERROR);

    }
    // -------------------------------------------------------------------------------
    public function delete(PurchaseInvoice $invoice)
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
    // -------------------------------------------------------------------------------
    // -------------------------------------------------------------------------------
    public function inventoryIndex()
    {
//        $products = DB::table('products')
//            ->leftjoin('purchase_invoice_goods', 'products.id', '=', 'purchase_invoice_goods.product_id')
//            ->leftjoin('warehouse_outgo_goods', 'products.id', '=', 'warehouse_outgo_goods.product_id')
//            ->select(
//                'products.id' , 'products.fake' , 'products.title' , 'products.sku' ,
//                'products.option_1' , 'products.option_2', 'products.parent' , 'products.is_published' ,
//                'purchase_invoice_goods.product_id' , 'warehouse_outgo_goods.product_id' , 'warehouse_outgo_goods.quantity' ,
//                'purchase_invoice_goods.quantity',
//                DB::raw('COALESCE(SUM(purchase_invoice_goods.quantity) , 0 ) as totalBuy') ,
//                DB::raw('COALESCE(SUM(warehouse_outgo_goods.quantity) , 0 ) as totalSell') ,
//                DB::raw('(COALESCE(SUM(purchase_invoice_goods.quantity) , 0 ) - COALESCE(SUM(warehouse_outgo_goods.quantity),0) ) as stock'),
//                DB::raw('(CASE WHEN products.parent IS NULL THEN concat("SKU-", products.sku) ELSE concat("SKU-", products.parent , "-" , products.sku) END) as final_sku'),
//                DB::raw('(CASE WHEN products.parent IS NULL THEN products.title ELSE (select p.title from products p where p.sku = products.parent ) END) as final_title')
//            )
//            ->where('products.fake' , 'N')
//            ->where('products.is_published' , 'Y')
//            ->whereRaw('(select COUNT(e.id) from products e where products.sku = e.parent ) = 0')
//            ->groupBy('products.id' , 'purchase_invoice_goods.quantity' , 'warehouse_outgo_goods.quantity')
//            ->get();

        return view('warehouse::inventory.list')->with('title' , 'Warehouse Inventory');
    }
    // -------------------------------------------------------------------------------
    public function inventoryData()
    {
        $attributes = AttributeValue::pluck('title' , 'id');
        $units = Unit::pluck('title' , 'id');

        return Datatables::of(
            DB::table('products')
                ->leftjoin('purchase_invoice_goods', 'products.id', '=', 'purchase_invoice_goods.product_id')
                ->leftjoin('warehouse_outgo_goods', 'products.id', '=', 'warehouse_outgo_goods.product_id')
                ->select(
                    'products.id' , 'products.fake' , 'products.title' , 'products.sku' ,
                    'products.option_1' , 'products.option_2', 'products.parent' , 'products.is_published' ,
                    'purchase_invoice_goods.product_id' , 'warehouse_outgo_goods.product_id' , 'warehouse_outgo_goods.quantity' ,
                    'purchase_invoice_goods.quantity',
                    DB::raw('COALESCE(SUM(purchase_invoice_goods.quantity) , 0 ) as totalBuy') ,
                    DB::raw('COALESCE(SUM(warehouse_outgo_goods.quantity) , 0 ) as totalSell') ,
                    DB::raw('(COALESCE(SUM(purchase_invoice_goods.quantity) , 0 ) - COALESCE(SUM(warehouse_outgo_goods.quantity),0) ) as stock'),
                    DB::raw('(CASE WHEN products.parent IS NULL THEN concat("SKU-", products.sku) ELSE concat("SKU-", products.parent , "-" , products.sku) END) as final_sku'),
                    DB::raw('(CASE WHEN products.parent IS NULL THEN products.title ELSE (select p.title from products p where p.sku = products.parent ) END) as final_title'),
                    DB::raw('(CASE WHEN products.parent IS NULL THEN products.mainUnit_id ELSE (select p.mainUnit_id from products p where p.sku = products.parent ) END) as final_unit')
                )
                ->where('products.fake' , 'N')
                ->where('products.is_published' , 'Y')
                ->where('purchase_invoice_goods.deleted_at' , null)
                ->whereRaw('(select COUNT(e.id) from products e where products.sku = e.parent ) = 0')
//                ->groupBy('products.id')
                ->groupBy('products.id' , 'purchase_invoice_goods.quantity' , 'warehouse_outgo_goods.quantity')
                ->get()
            )
            ->editColumn('final_title' , function ($item) use ($attributes) {
                $string = $item->final_title ;
                if($item->option_1) {
                    $string.= '-'.$attributes[$item->option_1];
                }
                if($item->option_2) {
                    $string.= '-'.$attributes[$item->option_2];
                }
//                if($item->option_3) {
//                    $string.= '-'.$attributes[$item->option_3];
//                }

                return '<span >'.$string.'</span>';
            })
            ->editColumn('stock' , function ($item) {
                $res = round($item->stock , 3);
                return '<span class="bold '.generateTextColor($res).'">'.$res.'</span>';
            })
            ->editColumn('totalSell' , function ($item) {
                $res = round($item->totalSell , 3);
                return '<span class="bold">'.$res.'</span>';
            })
            ->editColumn('totalBuy' , function ($item) {
                $res = round($item->totalBuy , 3);
                return '<span class="bold">'.$res.'</span>';
            })
            ->editColumn('final_unit' , function ($item) use ($units) {
                return '<span class="badge badge-inverse">'.$units[$item->final_unit].'</span>';
            })
            ->rawColumns(['final_title', 'stock' , 'totalSell' , 'totalBuy' , 'final_unit'])
            ->make(true);
    }
    // -------------------------------------------------------------------------------
    public function pdf()
    {
        $units = Unit::pluck('title' , 'id');
        $attributes = AttributeValue::pluck('title' , 'id');

        $items = DB::table('products')
            ->leftjoin('purchase_invoice_goods', 'products.id', '=', 'purchase_invoice_goods.product_id')
            ->leftjoin('warehouse_outgo_goods', 'products.id', '=', 'warehouse_outgo_goods.product_id')
            ->select(
                'products.id' , 'products.fake' , 'products.title' , 'products.sku' ,
                'products.option_1' , 'products.option_2', 'products.parent' , 'products.is_published' ,
                'purchase_invoice_goods.product_id' , 'warehouse_outgo_goods.product_id' , 'warehouse_outgo_goods.quantity' ,
                'purchase_invoice_goods.quantity',
                DB::raw('COALESCE(SUM(purchase_invoice_goods.quantity) , 0 ) as totalBuy') ,
                DB::raw('COALESCE(SUM(warehouse_outgo_goods.quantity) , 0 ) as totalSell') ,
                DB::raw('(COALESCE(SUM(purchase_invoice_goods.quantity) , 0 ) - COALESCE(SUM(warehouse_outgo_goods.quantity),0) ) as stock'),
                DB::raw('(CASE WHEN products.parent IS NULL THEN concat("SKU-", products.sku) ELSE concat("SKU-", products.parent , "-" , products.sku) END) as final_sku'),
                DB::raw('(CASE WHEN products.parent IS NULL THEN products.title ELSE (select p.title from products p where p.sku = products.parent ) END) as final_title'),
                DB::raw('(CASE WHEN products.parent IS NULL THEN products.mainUnit_id ELSE (select p.mainUnit_id from products p where p.sku = products.parent ) END) as final_unit')
            )
            ->where('products.fake' , 'N')
            ->where('products.is_published' , 'Y')
            ->where('purchase_invoice_goods.deleted_at' , null)
            ->whereRaw('(select COUNT(e.id) from products e where products.sku = e.parent ) = 0')
            ->groupBy('products.id' , 'purchase_invoice_goods.quantity' , 'warehouse_outgo_goods.quantity')
            ->get();

        return $this->generate_pdf($items , $units , $attributes);
    }
    // -------------------------------------------------------------------------------
    private function generate_pdf($items , $units , $attributes) {

        $date = JalaliDate::fromDateTime( Carbon::now() )->format('D ، d M y');
        $pdf = PDF::loadView('warehouse::inventory.pdf.inventory', compact('items' , 'units', 'date' , 'attributes' ));
        return $pdf->stream('Inventory-report.pdf');
    }





}

