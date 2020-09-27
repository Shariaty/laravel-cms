<?php

namespace Modules\Sale\Http\Controllers;

use App\Jobs\SendSingleSMS;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\Products\AttributeValue;
use Modules\Products\Product;
use Modules\Sale\SaleInvoice;
use Modules\sale\Goods;
use Opilo\Farsi\JalaliDate;
use Yajra\Datatables\Datatables;


class OrderController extends Controller
{
    protected  $redirectPath = 'administrator/orders/list';

    protected $rules = array(
        'title'      => 'required|max:100'
    );

    public function index()
    {
        return view('sale::orders.list')->with('title' , 'Orders List');
    }
    // -------------------------------------------------------------------------------
    public function anyData()
    {
        return Datatables::of(SaleInvoice::siteOrders()->get())
            ->editColumn('is_paid' , function ($item) {
                return generatePaymentStatusBadge($item->is_paid);
            })
            ->editColumn('status' , function ($item) {
                return generateOrderStatusBadge($item->status);
            })
            ->editColumn('total_price' , function ($item) {
                return '<span class="price">'.$item->total_price.'</span>';
            })
            ->editColumn('created_at' , function ($item) {
                return '<span>'. JalaliDate::fromDateTime( $item->created_at )->format('D ، d M y') .'</span>';
            })
            ->addColumn('action' , function ($item) {
                return $this->render($item);
            })
            ->make(true);
    }
    // -------------------------------------------------------------------------------
    public function render( $item ) {
        $final = '';
        $final .= '<a href="'.route('admin.order.detail' , $item).'" class="btn btn-xs btn-info"><i class="fa fa-search"></i> نمایش جزییات</a>';
        return $final;
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
            $invoice->wareHouseOutGo->forceDelete();
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

