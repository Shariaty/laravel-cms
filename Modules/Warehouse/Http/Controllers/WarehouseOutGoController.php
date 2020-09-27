<?php

namespace Modules\Warehouse\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\Products\AttributeValue;
use Modules\Products\Product;
use Modules\Warehouse\Goods;
use Modules\Warehouse\PurchaseInvoice;
use Modules\Warehouse\WarehouseOutGo;
use Opilo\Farsi\JalaliDate;
use Yajra\Datatables\Datatables;


class WarehouseOutGoController extends Controller
{
    protected  $redirectPath = 'administrator/warehouse/outgo/list';

    protected $rules = array(
        'title'      => 'required|max:100'
    );

    public function index()
    {
        return view('warehouse::outgo.list')->with('title' , 'Warehouse Outgo');
    }
    // -------------------------------------------------------------------------------
    public function anyData()
    {
        return Datatables::of(WarehouseOutGo::with('saleInvoice')->get())
            ->editColumn('created_at' , function ($item) {
                return '<span>'. JalaliDate::fromDateTime( $item->created_at )->format('D ، d M y') .'</span>';
            })
            ->editColumn('invoice_number' , function ($item) {

                if ($item->saleInvoice){
                    return '<span class="badge badge-info"> برای شماره فاکتور :'. $item->saleInvoice->invoice_number .' </span>';
                } else {
                    return '<span class="badge badge-default">بدون فاکتور فروش</span>';
                }
            })
            ->addColumn('action' , function ($item) {
                return $this->render($item);
            })
            ->rawColumns(['invoice_number' , 'action' , 'created_at'])
            ->make(true);
    }
    // -------------------------------------------------------------------------------
    public function render( $item ) {
        $final = '';
        $final .= '<a href="'. route('admin.warehouse.outgo.detail' , $item->code).'" class="btn btn-xs btn-default"><i class="fa fa-search"/> <span class="farsi-text">نمایش جزییات حواله خروج</span> </a>';
//        $final .= '<a data-id="'.$item->id.'" class="btn btn-xs red delete_btn"><i class="fa fa-trash"></i></a>';
        return $final;
    }
    // -------------------------------------------------------------------------------
    public function view($code) {
        $invoice = WarehouseOutGo::whereCode($code->code)->with(['saleInvoice' , 'wGoods'])->first();
        $invoiceGoods = $invoice->wGoods()->with(['product.mainUnit' , 'product.subUnit'])->get();
//        dd($invoice);

        return view('warehouse::outgo.detail'
            , compact('invoice' , 'invoiceGoods')
        )->with('title' , 'Transfer number : '.$invoice->code);
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

}
