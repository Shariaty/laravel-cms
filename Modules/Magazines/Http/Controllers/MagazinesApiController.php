<?php

namespace Modules\Magazines\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Response as FacadeResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Facades\Image;
use Modules\Magazines\Magazine;
use Modules\Magazines\MagazineCategory;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\File;


class MagazinesApiController extends Controller
{
    protected  $destinationPathOfMagazines = PATH_ROOT.('/uploads/admins/magazine-pictures');
    protected  $destinationPathOfMagazineFiles = PATH_ROOT.('/uploads/admins/magazine-files');

    public function getCategoryInfo() {

        $firstCat = MagazineCategory::where('slug' , 'noskheh')->first();
        if($firstCat) {
            $data = $firstCat->magazines ;
            return response()->json([ 'firstCategory' => $firstCat , 'items' => $data ,'status' => true ]);
        } else {
            return response()->json([ 'firstCategory' => 'no data found' , 'status' => false]);
        }
    }

    public function downloadFile(Magazine $magazine)
    {
        if($magazine->file) {
            $file_path = $this->destinationPathOfMagazineFiles .'/'. $magazine->file;

            if(file_exists($file_path)) {

               @@$magazine->update(array('downloads' => $magazine->downloads+1));

                return FacadeResponse::download($file_path, $magazine->file, [
                    'Content-Length: '. filesize($file_path)
                ]);

            } else
            {
                exit('Requested file does not exist on our server!');
            }
        }   else
        {
            exit('Requested file does not exist on our server!');
        }
    }
}
