<?php
namespace App\Http\Controllers\Admin;

ini_set('max_execution_time', 10);

use App\Admin;
use App\Http\Controllers\Controller;
use App\User;
use App\Visitor;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Modules\Products\Product;
use Modules\Sale\Goods;
use Modules\Sale\SaleInvoice;
use Nwidart\Modules\Facades\Module;


class DashboardController extends Controller
{
    public function index()
    {
        $visitors = DB::table('visitors')->select( DB::raw('count(*) as total') , DB::raw('CONCAT_WS("/",month(created_at),year(created_at)) as monthYear'))
            ->groupby('monthYear')
            ->orderBy('monthYear' , 'ASC')
            ->limit(12)
            ->get('total' , 'monthYear');

        $array = [];
        foreach ($visitors as $d) {
            $array[]= [
                $d->monthYear ,
                $d->total
            ];
        }

        $countryReport = DB::table('visitors')->select( DB::raw('count(*) as visits') , 'country')
            ->groupby('country')
            ->limit(12)
            ->get('visits' , 'country');


        foreach ($countryReport as $d){
            $d->color  = generateColor() ;
        }

        $data = DB::table('users')
            ->select(DB::raw('count(*) as total') , DB::raw('CONCAT_WS("/",month(created_at),year(created_at)) as monthYear'))
            ->groupby('monthYear')
            ->orderBy('monthYear' , 'ASC')
            ->limit(12)
            ->get('total' , 'monthYear');

        $candidateArray = [];
        foreach ($data as $d) {
            $candidateArray[]= [
                $d->monthYear ,
                $d->total
            ];
        }

        $orderArray = DB::table('sale_invoice')
            ->select( DB::raw('count(*) as total') , DB::raw('CONCAT_WS("/",month(created_at),year(created_at)) as monthYear'))
            ->where('type' , SITE_ORDER)
            ->groupby('monthYear')
            ->orderBy('monthYear' , 'ASC')
            ->limit(12)
            ->get('total' , 'monthYear');

        foreach ($orderArray as $d){
            $d->color  = generateColor() ;
        }

        $lastActivities = Admin::orderBy('last_active' , 'DESC')->limit(5)->get();
        return view('admin.dashboard' , compact('array' , 'countryReport' , 'candidateArray', 'orderArray' , 'lastActivities'))->with('title' , 'Dashboard');
    }

    public function mediaManager()
    {
        return view('admin.fileManager')->with('title' , 'File Manager');
    }
}
