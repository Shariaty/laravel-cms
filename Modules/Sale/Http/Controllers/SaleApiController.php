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
use Modules\Sale\Goods;
use Modules\Sale\SaleInvoice;
use Modules\Warehouse\WGoods;
use Yajra\Datatables\Datatables;
ini_set('max_execution_time', 10);


class SaleApiController extends Controller
{
    protected $rules = array(
        'title'      => 'required|max:100'
    );

    public function create(Request $request)
    {
        $saleCont = new SaleController();
        $rand = $saleCont->generateFactorNumber();

        $user = auth()->guard('api')->user();
        $items = $request->input('items');

        $productResults = [];
        $productStock = [];
        $ids = [];
        foreach ($items as $item) {
            $product = Product::whereId($item['id'])->first();

            //Check The product Stock
//            $productStock[] = [ 'product' => $product ];
            $productStock = [];
            //Check The product Stock


            //Check The product Sale Limitation
            $limitation = null;
            if ($product->has_limit === 'Y') {
                $limitation = ['item_id' =>$item['id'] ,  'p_id' => $product->id , 'value' => $product->limit_value , 'timing' => $product->limit_time];
            } else {
                if ($product->parentProduct) {
                    if ( $product->parentProduct->has_limit === 'Y') {
                        $limitation = ['item_id' =>$item['id'] ,'p_id' => $product->parentProduct->id ,  'value' => $product->parentProduct->limit_value , 'timing' => $product->parentProduct->limit_time];
                    }
                }

            }
            if ($limitation){
                switch ($limitation['timing']) {
                    case 1 : $userPurchase = $this->generateQuery($user , $product , PRODUCT_LIMIT_DAY); break;
                    case 2 : $userPurchase = $this->generateQuery($user , $product , PRODUCT_LIMIT_WEEK); break;
                    case 3 : $userPurchase = $this->generateQuery($user , $product , PRODUCT_LIMIT_MONTH); break;
                    case 4 : $userPurchase = $this->generateQuery($user , $product , PRODUCT_LIMIT_YEAR); break;
                    default : $userPurchase = $user->orderGoods->where('product_id' , $product->id)->sum('final_quantity'); break;
                }

                $totalUserPurchased = $userPurchase + $item['final_quantity'];

                if ($totalUserPurchased > $limitation['value']) {
                    $ids[] = $limitation['item_id'];

                    $productResults[] = [
                        'limitValue' => $limitation['value'],
                        'per' => getLimitTimingName($limitation['timing']),
                        'userPurchased' => $userPurchase,
                        'totalUserPurchased' => $userPurchase + $item['final_quantity'] ,
                        'BuyingAtTheMoment' => $item['final_quantity'],
                        'message' => 'برخی از محصولات سبد درخواستی شما بیش از تعداد قابل خرید مجاز می باشد. لطفا سبد خرید خود را مجدداً بررسی نمایید.' ,
                    ];
                }
            }
            //Check The product Sale Limitation
        }

        if($productResults || $productStock) {
            return response()->json(['status' => 'l_error' ,
                'message' => 'برخی از محصولات سبد درخواستی شما بیش از تعداد قابل خرید مجاز می باشد. لطفا سبد خرید خود را مجدداً بررسی نمایید.' ,
                'productResults' => $productResults ,
                'productStock' => $productStock ,
                'ids' => $ids
            ]);
        } else {
            $finalResult = DB::transaction(function () use ($request , $rand , $user){

                $invoice = SaleInvoice::create([
                    'title' => 'سفارش وب سایت',
                    'user_id' => $user->id ? $user->id : null,
                    'type' => SITE_ORDER ,
                    'status' => ORDER_WAIT_TO_CONFIRM ,
                    'total_price' => $request->input('total_price'),
                    'total_qty' => $request->input('total_qty'),
                    'address_id' => $request->input('address'),
                    'invoice_number' => $rand
                ]);

                $data= [];
                if(count($request->input('items')) > 0){
                    foreach ($request->input('items') as $item) {
                        $data[] = new Goods ([
                            'product_id' => $item['id'] ,
                            'sale_invoice_id' => $invoice->id ,
                            'sale_price' => isset($item['price']) ? $item['price'] : 0 ,
                            'quantity' => isset($item['quantity']) ? $item['quantity'] : 0 ,
                            'unit_id' => isset($item['saleUnit']) ? $item['saleUnit'] : null ,
                            'final_quantity' => isset($item['final_quantity']) ? $item['final_quantity'] : 0 ,
                        ]);
                    }
                }
                $invoice->goods()->saveMany($data);


                $Wdata = [];
                $wareHouseOutGo = $invoice->wareHouseOutGo()->create(['code' => $rand]);
                if(count($request->input('items')) > 0){
                    foreach ($request->input('items') as $item ) {
                        $product = Product::whereId( $item['id'] )->with(['bom.rawProduct' , 'parentProduct'])->first();

                        if (count($product->bom)) {
                            foreach ($product->bom as $bom) {
                                $unit = $item['final_quantity'];
                                $Wdata[] = new WGoods ([
                                    'product_id' => $bom->rawProduct_id ,
                                    'unit_id' => $bom->rawProduct->mainUnit_id,
                                    'warehouse_outgo_id' => $wareHouseOutGo->id ,
                                    'quantity' => $unit * $bom->value
                                ]);
                            }
                        }
                        else
                        {
                            $Wdata[] = new WGoods ([
                                'product_id' => $item['id'] ,
                                'unit_id' => $product->parent ? $product->parentProduct->mainUnit_id : $product->mainUnit_id ,
                                'warehouse_outgo_id' => $wareHouseOutGo->id ,
                                'quantity' => $item['final_quantity']
                            ]);
                        }
                    }
                }
                $wareHouseOutGo->wGoods()->saveMany($Wdata);

                return true;
            });

            if($finalResult) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'سفارش شما با موفقیت دریافت شد' ,
                    'codePeigiri' => $rand
                ]);
            } else {
                return response()->json(['status' => 'error' , 'message' => LBL_COMMON_ERROR]);
            }
        }
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
    public function getListOfOrders(Request $request)
    {
        $orderList = [];
        $user = auth()->guard('api')->user();

        if($user) {
            $orders = $user->orders;

            if (count($orders)) {
                foreach ($orders as $order) {
                    $orderList[] = [
                        'invoice_number' => $order->invoice_number ,
                        'is_paid' => $order->is_paid,
                        'status' => $order->status,
                        'total_price' => $order->total_price,
                        'created_at' => $order->created_at ,
                        'updated_at' => $order->updated_at
                    ];
                }
            }

        }

        return response()->json(['orders' => $orderList]);
    }
    // -------------------------------------------------------------------------------
    function generateQuery ($user , $product , $limit){
//        $user = User::whereId($user->id)->first();

        $result = $user->orderGoods()
            ->whereHas('saleInvoice' , function ($q) use ($limit) {
                $q->where( 'created_at', '>', Carbon::now()->subDays($limit));
            })
            ->where('product_id' , $product->id)
            ->sum('final_quantity');

        return $result;
    }
    // -------------------------------------------------------------------------------

}

