<?php

namespace Modules\Sale\Http\Controllers;

use App\Jobs\SendSingleSMS;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\Products\AttributeValue;
use Modules\Products\Product;
use Modules\Sale\PriceList;
use Modules\Sale\PriceListItem;
use Modules\Sale\SaleInvoice;
//use niklasravnsborg\LaravelPdf\Pdf;
use niklasravnsborg\LaravelPdf\Facades\Pdf;
use Opilo\Farsi\JalaliDate;
use Yajra\Datatables\Datatables;


class PriceListController extends Controller
{
    protected  $redirectPath = 'administrator/priceList/list';

    protected $rules = array(
        'title'      => 'required|max:100'
    );

    public function index()
    {
        return view('sale::priceList.list')->with('title' , 'Price List');
    }
    // -------------------------------------------------------------------------------
    public function anyData()
    {
        return Datatables::of(PriceList::all())
            ->editColumn('code' , function ($item) {
                return '<span>'.$item->code.'</span>';
            })
            ->editColumn('created_at' , function ($item) {
                return '<span>'. JalaliDate::fromDateTime( $item->created_at )->format('D ، d M y') .'</span>';
            })
            ->editColumn('updated_at' , function ($item) {
                return '<span>'. JalaliDate::fromDateTime( $item->updated_at )->format('D ، d M y') .'</span>';
            })
            ->addColumn('action' , function ($item) {
                return $this->render($item);
            })
            ->rawColumns(['code', 'created_at' , 'updated_at' , 'action'])
            ->make(true);
    }
    // -------------------------------------------------------------------------------
    public function render( $item ) {
        $final = '';
        $final .= '<a href="'.route('admin.priceList.edit' , $item->id).'" class="btn btn-xs btn-warning"><i class="fa fa-edit"></i> ویرایش لیست قیمت</a>';
        $final .= '<a data-id="'.$item->id.'" class="btn btn-xs red delete_btn"><i class="fa fa-trash"></i></a>';
        $final .= '<a href="'.route('admin.priceList.pdf' , $item->id).'" class="btn btn-xs btn-info"><i class="fa fa-file-pdf-o"></i></a>';
        return $final;
    }
    // -------------------------------------------------------------------------------
    public function pdf($id)
    {
        $priceList = PriceList::whereId($id)->with('items.product')->first();
        return $this->generate_pdf($priceList);
    }
    // -------------------------------------------------------------------------------
    private function generate_pdf($data) {

        $date  = $data->created_at;
        $items = $data->items;

        $pdf = PDF::loadView('sale::pdf.priceList', compact('date' , 'items'));
        return $pdf->stream('priceList.pdf');
    }
    // -------------------------------------------------------------------------------
    public function add() {
        $products = Product::notFake()->notRaw()->get();
        $attributes = AttributeValue::pluck('title' , 'id');

        $productListForJs = [];

        foreach ($products as $product) {
            if(!$product->parent && $product->sub_product_count == 0) {
                $productListForJs[] = (object)[ 'id' => $product->id , 'text' => $product->title , 'sku' => $product->visible_sku];
            }

            if(!$product->parent && $product->sub_product_count > 0) {
                foreach ($product->subs as $subProduct) {
                    $productListForJs[] = (object)[ 'id' => $subProduct->id , 'text' => $this->generate($subProduct , $product , $attributes) , 'sku' => $subProduct->visible_sku];
                }
            }
        }

        return view('sale::priceList.add' , compact('productListForJs'))->with('title' , 'Create Price List');
    }
    // -------------------------------------------------------------------------------
    public function create(Request $request)
    {
        $user = getCurrentAdminUser();
        $rand = mt_rand(111111 , 999999);
        $list = PriceList::create(['code' => $rand , 'user_id' => $user->id]);
        $prices = $request->input('price');
        $sku = $request->input('sku');
        $titles = $request->input('titles');

        $data= [];
        if(count($request->input('item')) > 0){
            foreach ($request->input('item') as $key => $value) {
                $data[] = new PriceListItem ([
                    'product_id' => $value ,
                    'sale_invoice_id' => $list->id ,
                    'price' => isset($prices[$key]) ? array_first($prices[$key]) : 0 ,
                    'title' => isset($titles[$key]) ? $titles[$key] : 'no title' ,
                    'sku' => isset($sku[$key]) ? $sku[$key] : 'no sku'
                ]);
            }
        }
        $list->items()->saveMany($data);
        return redirect(route('admin.priceList.list'))->with('success' , 'PriceList has been created');
    }
    // -------------------------------------------------------------------------------
    public function edit($id) {
        $products = Product::notFake()->notRaw()->get();
        $attributes = AttributeValue::pluck('title' , 'id');
        $productListCustom = [];
        foreach ($products as $product) {
            if(!$product->parent && $product->sub_product_count == 0) {
                $productListCustom[$product->id] = (object)[ 'id' => $product->id , 'text' => $product->title , 'sku' => $product->visible_sku];
            }

            if(!$product->parent && $product->sub_product_count > 0) {
                foreach ($product->subs as $subProduct) {
                    $productListCustom[$subProduct->id] = (object)[ 'id' => $subProduct->id , 'text' => $this->generate($subProduct , $product , $attributes) , 'sku' => $product->visible_sku];
                }
            }
        }

        $priceList = PriceList::whereId($id)->with('items')->first();
        return view('sale::priceList.edit' , compact('priceList' , 'productListCustom'))->with('title' , 'Edit Price List');
    }
    // -------------------------------------------------------------------------------
    public function update(PriceList $priceList , Request $request)
    {
        $prices = $request->input('price');
        $sku = $request->input('sku');
        $titles = $request->input('titles');
        $data = [];
        if(count($request->input('item')) > 0)
        {
            foreach ($request->input('item') as $key => $value)
            {
                $data[] = new PriceListItem ([
                    'product_id' => $value ,
                    'sale_invoice_id' => $priceList->id ,
                    'price' => isset($prices[$key]) ? array_first($prices[$key]) : 0 ,
                    'title' => isset($titles[$key]) ? $titles[$key] : 'no title' ,
                    'sku' => isset($sku[$key]) ? $sku[$key] : 'no sku'
                ]);
            }
        }

        $result = DB::transaction( function () use ($priceList , $data) {
            if($priceList->items()->forceDelete()) {
                $priceList->items()->saveMany($data);
                return true;
            }
            return false;
        });

        if ($result)
            return redirect(route('admin.priceList.list'))->with('success' , LBL_COMMON_UPDATE_SUCCESSFUL);

        return redirect(route('admin.priceList.list'))->with('error' , LBL_COMMON_ERROR);
    }
    // -------------------------------------------------------------------------------
    public function delete(PriceList $priceList)
    {
        $result = DB::transaction( function () use ($priceList) {
            if($priceList->delete()) {
                $priceList->items()->delete();
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



    public function view(SaleInvoice $invoice) {
        $invoiceGoods = $invoice->goods()->with(['product.mainUnit' , 'product.subUnit'])->get();
        return view('sale::orders.detail'
            , compact('invoice' , 'invoiceGoods')
        )->with('title' , 'Order Invoice : '.$invoice->invoice_number);
    }

    public function updateStatus(SaleInvoice $invoice) {
        if($invoice) {
            if($invoice->status <= 2) {
//              if ($invoice->status === ORDER_WAIT_TO_CONFIRM && $invoice->type === SITE_ORDER){
                if ($invoice->status === ORDER_WAIT_TO_CONFIRM){
                    $smsData = [
                        'cell' => $invoice->user->cell ,
                        'text' => 'وضعیت سفارش شما با کد '. $invoice->invoice_number .' به آماده ارسال تغییر یافت'.PHP_EOL.env('APP_NAME') ,
                    ];
                    // Send the activation code
                    @SendSingleSMS::dispatch($smsData);
                }
                $invoice->status = $invoice->status +1;
                $invoice->update();
                return back()->with('success' , 'Order status updated successfully!');
            }
            return back()->with('error' , LBL_COMMON_ERROR);
        }
        return back()->with('error' , LBL_COMMON_ERROR);
    }
    // -------------------------------------------------------------------------------
    public function cancelOrder(SaleInvoice $invoice)
    {
        if($invoice) {
            if($invoice->status) {
                $invoice->status = ORDER_CANCELED;
                $invoice->update();
                return back()->with('warning' , 'Order has been canceled!');
            }
            return back()->with('error' , LBL_COMMON_ERROR);
        }
        return back()->with('error' , LBL_COMMON_ERROR);
    }

}

