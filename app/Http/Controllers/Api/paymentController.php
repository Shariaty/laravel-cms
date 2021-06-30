<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Api\Auth\RegisterController;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;
use JWTAuth;
use Modules\Sale\SaleInvoice;
use Tymon\JWTAuth\Exceptions\JWTException;

use Larabookir\Gateway\Exceptions\InvalidRequestException;
use Larabookir\Gateway\Exceptions\NotFoundTransactionException;
use Larabookir\Gateway\Exceptions\PortNotFoundException;
use Larabookir\Gateway\Exceptions\RetryException;
use Larabookir\Gateway\Zarinpal\ZarinpalException;
use Mockery\Exception;


class paymentController extends Controller
{
//    private $url = 'http://localhost:3000/';
    private $url = 'http://irandetpak.ir/';

    public function paymentHistory(Request $request)
    {

        $user = JWTAuth::parseToken()->authenticate();
        $mainUser = $user->transactions()->orderBy('created_at' , 'DESC')->paginate(12);

        return response([
            'data' => $mainUser,
            'status' => 'success'
        ]);

    }

    public function pay(Request $request)
    {
        if($request->has('code')) {
            $code = $request->input('code');
            $invoice = SaleInvoice::where('invoice_number' , $code)->first();

            if($invoice) {
                try {
                    $gateway = \Gateway::zarinpal();
                    $gateway->setCallback(url('api/callBackFromBank'));
                    $gateway->price($invoice->total_price * 10)->ready();
                    $refId =  $gateway->refId();
                    $transID = $gateway->transactionId();

                    $invoice->transaction_id = $transID;
                    $invoice->update();

                    return $gateway->redirect();

                } catch (Exception $e) {

                    echo $e->getMessage();
                }
            }
            return 'فاکتور وجود ندارد';
        }
        return LBL_COMMON_ERROR;
    }

    public function callBack(Request $request)
    {
        $error          = null;
        $trackingCode   = null;
        $title          = null;
        $type           = null;
        $invoiceID      = null;

        $transaction_id = $request->input('transaction_id');
        $invoice = SaleInvoice::where('transaction_id' , $transaction_id)->first();

        if ($invoice) {
            $invoiceID = $invoice->invoice_number;
        }

        try {
            $title          = 'Verify';
            $type           = 'success';

            $gateway = \Gateway::verify();
            $trackingCode = $gateway->trackingCode();
            $refId = $gateway->refId();
            $cardNumber = $gateway->cardNumber();

            // Your code here
            if($invoiceID) {
                $invoice->is_paid = 'Y';
                $invoice->update();
            }

            return redirect($this->url.'result?status=ok&orderId='.$invoiceID);

        }

        catch (RetryException $e)
        {
            $title          = 'Error';
            $type           = 'danger';
            $error = $e->getMessage();
        }
        catch (PortNotFoundException $e)
        {
            $title          = 'Error';
            $type           = 'danger';
            $error = $e->getMessage();
        }
        catch (InvalidRequestException $e)
        {
            $title          = 'Error';
            $type           = 'danger';
            $error = $e->getMessage();
        }
        catch (NotFoundTransactionException $e)
        {
            $title          = 'Error';
            $type           = 'danger';
            $error = $e->getMessage();
        }
        catch (ZarinpalException $e)
        {
            $title          = 'Error';
            $type           = 'danger';
            $error = $e->getMessage();
        }
        catch (Exception $e)
        {
            $title          = 'Error';
            $type           = 'danger';
            $error = $e->getMessage();
        }

        return redirect($this->url.'result?status=error&orderId='.$invoiceID);


//        return view('payments.callback' , compact(
//            'title' , 'type' , 'invoiceID' ,
//            'error' , 'trackingCode' , 'cardNumber' , 'refId'));
    }
}
