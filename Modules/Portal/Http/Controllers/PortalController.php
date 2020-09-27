<?php

namespace Modules\Portal\Http\Controllers;

use App\Http\Controllers\CURL;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\Portal\Imports\AliasImport;
use Modules\Portal\Imports\PortalImport;
use Illuminate\Support\Facades\Auth;
use Modules\Portal\Portal;
use Modules\Portal\PortalAlias;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Portal\PortalRecord;
use Modules\Portal\PortalTempRecord;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\Validator;

class PortalController extends Controller
{
    protected  $redirectPath = 'administrator/portal/list';
    protected  $portalApiPath = 'https://zakeri-civil.portal.ir/site/api/v1/';
    protected  $user = '09124360925';
    protected  $pass = 'z123456';

    public function __construct()
    {
        set_time_limit(0);
        ob_implicit_flush(true);
    }

    // -------------------------------------------------------------------------------
    public function index()
    {
        $count = PortalAlias::count('id');
        return view('portal::list' , compact('count'))->with('title' , 'List of authorized Sku and Id`s');
    }
    // -------------------------------------------------------------------------------
    public function anyData()
    {
        $dataTable =  Datatables::of(PortalAlias::select(['id', 'sku' , 'portal_id' , 'is_published', 'created_at']))
            ->editColumn('created_at', function ($alias) {
                return $alias->created_at->format('Y/m/d');
            })
            ->editColumn('is_published', function ($alias) {
                return ($alias->is_published == 'N') ?
                    '<button class="btn btn-xs btn-default status-change" data-status="'.$alias->is_published.'" data-new="'.$alias->id.'"><i class="fa fa-ban fa-1x text-danger"></i></button>' :
                    '<button class="btn btn-xs btn-default status-change" data-status="'.$alias->is_published.'" data-new="'.$alias->id.'"><i class="fa fa-check fa-1x text-success"></i></button>' ;
            })
            ->addColumn('action' , function ($alias) {
                return $this->render($alias);
            })
            ->rawColumns(['is_published' , 'action'])
            ->make(true);

        return $dataTable;
    }
    // -------------------------------------------------------------------------------
    public function render( $alias ) {
        $final = null;
        $final .= '<a data-id="'.$alias->id.'" class="btn btn-xs red delete_btn"><i class="fa fa-trash"></i></a>';
        return $final;
    }
    // -------------------------------------------------------------------------------
    protected function delete(PortalAlias $alias){
        if($alias->delete()){
            return response()->json(['status' => 'success', 'message' => 'Item successfully removed']);
        }
        return response()->json(['status' => 'error', 'message' => 'There was problem in removing this item!']);
    }
    // -------------------------------------------------------------------------------
    protected function add(Request $request){
        $data = $request->input('data');

        if (isset($data[0]) && isset($data[1])){
            $sku = $data[0];
            $portal_id = $data[0];

            $item = PortalAlias::create(
                ['sku' => $sku , 'portal_id' => $portal_id , 'created_at' => Carbon::now()]
            );
            return response()->json(['status' => 'success', 'message' => 'Item saved']);

        }
        return response()->json(['status' => 'error', 'message' => 'provided data is not correct']);
    }
    // -------------------------------------------------------------------------------
    protected function statusUpdate(Request $request){
        if($request->has('user_id') && $request->has('status')){
            $user = PortalAlias::where('id' , $request->input('user_id'))->first();
            $user->is_published = $request->input('status');
            $user->update();
            return response(['status' => 'success' , 'message' => 'successfully updated' , 'newStatus' => $request->input('status')]);
        }
        return response(['status' => 'error' , 'message' => 'Something went wrong! contact the administrator'] , 404);

    }
    // -------------------------------------------------------------------------------
    protected function clearAll(){
        PortalAlias::truncate();
        return redirect($this->redirectPath)->with('success' , 'All Items has been removed');
    }
    // -------------------------------------------------------------------------------
    public function ajaxFileUpload(Request $request)
    {
        if($request->ajax()) {
            if($request->hasFile('file'))  {
                $rules = array('file' => 'required|mimes:xlsx|max:2048');
                $validator = Validator::make($request->all(), $rules);
                if($validator->fails()){
                    $errors = $validator->messages()->all();
                    $result = array('success' => false , 'message' => $errors);
                } else {
                    $file = $request->file('file');
                    Excel::import(new AliasImport, $file);
                    $newCount = PortalAlias::count('id');
                    $result = array('success' => true , 'message' => $newCount);
                }
                return response()->json($result);
            } else {
                return response()->json(array('error' => true , 'message' => [ 'File is not available or it is damaged , in this case you can use another file' ] ));
            }
        }
        return 'forbidden';
    }


    public function portalIndex()
    {
        $count = Portal::count('id');
        return view('portal::portal.list' , compact('count'))->with('title' , 'List of portal tasks');
    }
    // -------------------------------------------------------------------------------
    public function portalAnyData()
    {
        $dataTable =  Datatables::of(Portal::select(['id', 'created_at']))
            ->editColumn('created_at', function ($portal) {
                return $portal->created_at->format('Y/m/d');
            })
            ->addColumn('records', function ($portal) {
                return $portal->records()->count();
            })
            ->addColumn('state', function ($portal) {
                $failed =  $portal->records()->failed()->count();
                $succeed =  $portal->records()->succeed()->count();

                return "<span> <span style='color: red; font-weight: bold;'>Failed: $failed</span>  |  <span style='color: green; font-weight: bold;'>Succeed: $succeed</span> </span>";
            })
            ->addColumn('action' , function ($portal) {
                return $this->portalRender($portal);
            })
            ->rawColumns(['created_at' ,'action' , 'state'])
            ->make(true);

        return $dataTable;
    }
    // -------------------------------------------------------------------------------
    public function portalRender( $portal ) {
        $final = null;
        $final .= '<a href="'.route('admin.portal.records.list' , $portal->id).'" data-id="'.$portal->id.'" class="btn btn-xs yellow"><i class="fa fa-eye"></i></a>';
        $final .= '<a data-id="'.$portal->id.'" class="btn btn-xs red delete_btn"><i class="fa fa-trash"></i></a>';
        $final .= '<button title="Play the updater" type="button" class="btn btn-xs green play-updater" data-id="'.$portal->id.'"><i class="fa fa-play"></i></button>';

//        $final .= '<a title="Play the updater" href="'.route('admin.portal.taskPlayer' , ['portalTask' => $portal->id]).'" data-id="'.$portal->id.'" class="btn btn-xs green play-updater"><i class="fa fa-play"></i></a>';
        return $final;
    }
    // -------------------------------------------------------------------------------
    protected function portalDelete(Portal $portal){
        DB::beginTransaction();
        try {
            $portal->records()->forceDelete();
            $portal->forceDelete();
            DB::commit();
            $success = true;
        } catch (\Exception $e) {
            $success = false;
            DB::rollback();
        }

        if ($success) {
            return response()->json(['status' => 'success', 'message' => 'Item successfully removed']);
        }

        return response()->json(['status' => 'error', 'message' => 'There was problem in removing this item!']);
    }
    // -------------------------------------------------------------------------------
    public function portalAjaxFileUpload(Request $request)
    {
        if($request->ajax()) {
            if($request->hasFile('file'))  {
                $rules = array('file' => 'required|mimes:xlsx|max:2048');
                $validator = Validator::make($request->all(), $rules);
                if($validator->fails()){
                    $errors = $validator->messages()->all();
                    $result = array('success' => false , 'message' => $errors);
                } else {
                    $file = $request->file('file');

                    DB::beginTransaction();
                    try {
                        $portal = Portal::create([
                            'admin_id' =>  Auth::user() ? Auth::user()->id : null ,
                        ]);
                        Excel::import(new PortalImport($portal->id), $file);
                        $newCount = $portal->count();
                        $tempResults = PortalTempRecord::groupBy('sku')->select(
                            'admin_id' , 'sku' , 'portal_id' , 'created_at' , 'updated_at',
                            DB::raw('sum(eachStock) as stock'))->get()->toArray();

                        if ($tempResults && count($tempResults)){
                            PortalRecord::insert($tempResults);
                            $portal->tempRecords()->forceDelete();
                        }

                        DB::commit();
                        $success = true;
                    } catch (\Exception $e) {
                        $success = false;
                        DB::rollback();
                    }

                    if ($success) {
                        $result = array('success' => true , 'message' => isset($newCount) ? $newCount : 0);
                    } else {
                        return response()->json(array('error' => true , 'message' => [ 'Transaction Failed' ] ));
                    }
                }
                return response()->json($result);

            } else {
                return response()->json(array('error' => true , 'message' => [ 'File is not available or it is damaged , in this case you can use another file' ] ));
            }
        }
        return 'forbidden';
    }


    public function taskPlayer(Portal $portal)
    {
        if ($portal){
            $records = $portal->records()->get();
            $count = $records->count();
            if ($count) {
                $token = $this->getToken();
                if ($token) {
                    ob_start();
                    foreach ($records as $key => $record){
                        $alias = PortalAlias::where('sku' , $record->sku)->first();
                        $statusLast = ($key+1 == $count) ? true : false;

                        if ($alias && $alias->portal_id){
                            usleep(1000000 / 2);

                            // Get product info
                            $productData = $this->getProductInformation($alias->portal_id , $token);

                            if ($productData && isset($productData->success) && $productData->success) {
                                $data = $productData->variant;
                                $data = json_decode(json_encode($data), true);

                                unset($data["title"]);
                                $data["stock"] = $record->stock ? $record->stock : 0;

                                //Call the updater
                                $response = $this->productUpdate($alias->portal_id , $data , $token);
                                if ($response && isset($response->success) && $response->success) {
                                    $record->status = 1;
                                    $record->update();
                                    $this->out("Updated sku:$record->sku | Portal_ID:$alias->portal_id | Stock ($record->stock)" , 1 , $statusLast);
                                } else {
                                    $record->status = 2;
                                    $record->update();
                                    $this->out("UpdateFailed sku: $record->sku | Portal_ID:$alias->portal_id | Stock ($record->stock)" , 2 , $statusLast);
                                }
                            } else {
                                $record->status = 2;
                                $record->update();
                                $this->out("GetFailed sku:$record->sku | Portal_ID:$alias->portal_id | Stock ($record->stock)" , 2 , $statusLast);
                            }
                        } else {
                            $this->out("Unable sku:$record->sku | Portal_ID:Unknown | Stock ($record->stock)" , 3 , $statusLast);
                        }
                    }
                    ob_end_flush();
                    return;
                }
                return 'Token Problem!';
            }
            return 'No Task to execute. Abort!';
        }
        return 'Lack of data';
    }

    private function out($message, $status = null , $statusLast = false)
    {
        ob_flush();

        echo '<pre>';

        $state = 'Unknown';
        switch ($status) {
            case 1 : $state = '<span style="color: green;">SUCCESS</span>'; break;
            case 2 : $state = '<span style="color: red;">FAILED</span>'; break;
            case 3 : $state = '<span style="color: orange;">WARNING</span>'; break;
        }

        echo "[$message] -> $state<br/>";

        if ($statusLast) {
            echo '<br/><span style="color: green; font-weight: bold;">Finished</span>';
        }

        echo '</pre>';
    }

    private function getToken(){
        $urlLogin = $this->portalApiPath.'user/create-session';
        $paramLogin = array(
            'username' => $this->user,
            'password' => $this->pass
        );
        $token = null;
        $responseAuthenticate = json_decode(CURL::init()->execute($urlLogin, $paramLogin)->response());
        if ($responseAuthenticate && isset($responseAuthenticate->token) && $responseAuthenticate->token ){
            $token = $responseAuthenticate->token;
        }
        return $token;
    }

    private function getProductInformation($id ,$token){

        $url = $this->portalApiPath.'manage/store/products/variants/'.$id;

        $cURLConnection = curl_init();

        curl_setopt($cURLConnection, CURLOPT_URL, $url);
        curl_setopt($cURLConnection, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($cURLConnection, CURLOPT_HTTPHEADER, ['Content-Type: application/json' , "AUTHORIZATION: Bearer $token"]);
        $phoneList = curl_exec($cURLConnection);
        curl_close($cURLConnection);
        $response = json_decode($phoneList);

        return $response;
    }

    private function productUpdate($id , $data , $token){
        $urlLogin = $this->portalApiPath.'manage/store/products/variants/'.$id;
        return json_decode(CURL::init()->execute($urlLogin, $data , [] ,  'put' , $token)->response());
    }


    // Portal Records Viewer
    // -------------------------------------------------------------------------------
    public function portalRecordsIndex($portal_id)
    {
        if (!$portal_id){
            return 'lack of data';
        }
        $count = PortalRecord::where('portal_id' , $portal_id)->count('id');
        return view('portal::portal.records' , compact('count' , 'portal_id'))->with('title' , 'List of Task jobs');
    }
    // -------------------------------------------------------------------------------
    public function portalRecordsAnyData(Request $request)
    {
        $portal_id = $request->portaLId;

        $dataTable = Datatables::of(PortalRecord::select(['id', 'sku' , 'stock' , 'status'])->where('portal_id' , $portal_id)->get())
            ->editColumn('status', function ($record) {
                switch ($record->status) {
                    case '1' : return '<div style="color: darkgreen; font-weight: bold;">Success</div>';break;
                    case '2' : return '<div style="color: red; font-weight: bold;">Error</div>';break;
                   default : return '<div>No action</div>';break;
                }
            })
            ->rawColumns(['status'])
            ->make(true);

        return $dataTable;
    }
}
