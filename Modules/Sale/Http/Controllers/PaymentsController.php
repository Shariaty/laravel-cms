<?php
namespace Modules\Sale\Http\Controllers;
use App\Jobs\SendSingleSMS;
use App\Transaction;
use Carbon\Carbon;
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


class PaymentsController extends Controller
{
    protected  $redirectPath = 'administrator/payments/list';

    public function index()
    {
        return view('sale::payments.list')->with('title' , 'Payments List');
    }
    // -------------------------------------------------------------------------------
    public function anyData()
    {
        return Datatables::of(Transaction::with('invoice.user')->get())
            ->editColumn('invoice_number' , function ($item) {
                if($item->invoice){
                    return '<span>'.$item->invoice->invoice_number.'</span>';
                } else {
                    return '<span>--</span>';
                }
            })
            ->editColumn('user' , function ($item) {
                if($item->invoice){
                    if($item->invoice->user){
                        return '<span>'. $item->invoice->user->fullName .'</span>';
                    } else {
                        return '<span>--</span>';
                    }
                } else {
                    return '<span>--</span>';
                }
            })
            ->editColumn('cell' , function ($item) {
                if($item->invoice){
                    if($item->invoice->user){
                        return '<span>'. $item->invoice->user->cell .'<a style="margin-left: 8px" class="btn btn-xs btn-success" href="tel:'.$item->invoice->user->cell.'"><i class="fa fa-phone"/></a></span>';
                    } else {
                        return '<span>--</span>';
                    }
                } else {
                    return '<span>--</span>';
                }
            })
            ->editColumn('price' , function ($item) {
                return '<span class="price">'.($item->price / 1) .'</span>';
            })
            ->editColumn('port' , function ($item) {
                switch ($item->port) {
                    case 'ZARINPAL' : return '<span class="badge badge-warning">درگاه پرداخت زرین پال</span>'; break;
                    default : return '<span class="badge badge-default">درگاه پرداخت نامعلوم</span>'; break;
                }
            })
            ->editColumn('status' , function ($item) {
                switch ($item->status) {
                    case 'SUCCEED' : return '<span class="badge badge-success">موفق</span>'; break;
                    case 'FAILED' : return '<span class="badge badge-danger">نا موفق</span>'; break;
                    default : return '<span class="badge badge-default">وضعیت نامعلوم</span>'; break;
                }
            })
            ->editColumn('payment_date' , function ($item) {
                if($item->payment_date) {
                    return '<span>'. tarikhFarsiWithTime($item->payment_date).'</span>';
                }
                return '<span>--</span>';
            })
            ->addColumn('action' , function ($item) {
                return $this->render($item);
            })
            ->rawColumns(['invoice_number', 'user' , 'cell' , 'price' , 'port' , 'status' , 'payment_date' , 'action'])
            ->make(true);
    }
    // -------------------------------------------------------------------------------
    public function render( $item ) {
        $final = '';
//        $final .= '<a href="'.route('admin.order.detail' , $item).'" class="btn btn-xs btn-info"><i class="fa fa-search"></i> نمایش جزییات</a>';
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
                if ($invoice->status === ORDER_WAIT_TO_CONFIRM && $invoice->type === SITE_ORDER){
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

