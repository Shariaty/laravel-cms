<?php

namespace App\Http\Controllers\Api\Auth;

use App\Address;
use App\Http\Controllers\Controller;
use App\Jobs\SendSingleSMS;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\File;


class RegisterController extends Controller
{
    protected $registerRules = [
        'fullName'  => 'required|min:3|max:100',
        'cell'    =>  'required|numeric|unique:users',
        'password' => 'required|min:6'
    ];
    protected $profileUpdateRules = [
        'fullName'  => 'required|min:3|max:100',
        'email'     => 'required|email',
        'newPassword'  => 'sometimes|min:6'
    ];
    protected $addressRules = [
        'title'       => 'required|min:3|max:100',
        'address'     => 'required|min:3|max:250',
    ];
    protected $destinationPathOfUserAvatars = PATH_ROOT.('/uploads/admins/user-avatars');


    /** ------------------------------------------------------------------------------------------------------------- */
    public function register(Request $request)
    {
        $userCount = User::select('id')->where('cell' , $request->input('cell'))->get();

        if(count($userCount)) {
            return response()->json(['status' => 'error' , 'message' => 'این شماره قبلا استفاده شده است']);
        }

        $validator = Validator::make($request->all(), $this->registerRules);
        if ($validator->fails()) {
            return response()->json(['status' => 'validationError' , 'message' => $validator->errors()]);
        }

        $this->_newUserSituation($request);
        return response()->json(['status' => 'success']);

    }
    /** ------------------------------------------------------------------------------------------------------------- */
    protected function _newUserSituation(Request $request) {

        $activationCode = mt_rand(11111 , 99999);

        //Create user and send activation code
        $user = User::create([
            'fullName'          => $request->input('fullName'),
            'cell'              => $request->input('cell'),
            'password'          => bcrypt($request->input('password')),
            'activation_code'   => $activationCode
        ]);

        if($user){

            //Send Activation Code and return response
            $smsData = [
                'cell' => $user->cell ,
                'text' => $activationCode,
            ];

            // Send the activation code
            SendSingleSMS::dispatch($smsData);
            return true;
        }
        return false;
    }
    /** ------------------------------------------------------------------------------------------------------------- */
    public function activate(Request $request)
    {
        $activationCode = intval($request->input('activation_code'));
        $cell = $request->input('cell');

        $user = User::where('cell' , $cell)->first();

        if($user) {
            if($user->activation_code === $activationCode) {
                //Activate Account
                $user->status = PUS_ACTIVE ;
                $user->update();

                $userToken= JWTAuth::fromUser($user);
                if ($userToken) {
                    return response()->json(['status' => 'success' ,
                        'user' => $user->only(['avatar' , 'cell' , 'fullName']),
                        'token' => $userToken, 'message' => 'اکانت شما با موفقیت فعال شد']);
                }
            } else {
                return response()->json([
                    'status' => 'error',
                    'code db' => $user->fullName,
                    'code USer' => $activationCode,
                    'message' => 'کد وارد شده نا معتبر است'] , 270);
            }
        }
        return response()->json(['status' => 'error' , 'message' => LBL_COMMON_ERROR] , 270);
    }
    /** ------------------------------------------------------------------------------------------------------------- */
    public function sendCodeAgain(Request $request)
    {
        $activationCode = mt_rand(11111 , 99999);
        $cell = $request->input('cell');
        if($cell) {
            $user = User::where('cell' , $cell)->first();
            if($user) {
                $user->activation_code = $activationCode;
                $user->update();

                $smsData = [
                    'cell' => $user->cell ,
                    'text' => $activationCode ,
                ];

                // Send the activation code
                SendSingleSMS::dispatch($smsData);
                return response()->json(['status' => 'success' , 'message' => 'کد فعاسازی مجدد برای شما ارسال شد.']);
            }
            return response()->json(['status' => 'error' , 'message' => LBL_COMMON_ERROR]);
        }
        return response()->json(['status' => 'error' , 'message' => LBL_COMMON_ERROR]);
    }
    /** ------------------------------------------------------------------------------------------------------------- */
    public function updateUserInfo(Request $request)
    {
        $data = null;
        if ($request->input('newPassword')) {
            $data = $request->merge(array('password' => bcrypt($request->input('newPassword') )));
            $data = $request->except(['newPassword']);
        } else {
            $data = $request->except(['newPassword']);
        }

        $validator = Validator::make($data , $this->profileUpdateRules);
        if ($validator->fails()) {
            return response()->json(['status' => 'validationError' , 'message' => $validator->errors()]);
        }

        $user = auth()->guard('api')->user();
        $result = $user->update($data);

        if($result) {
            return response()->json([ 'status' => 'success' , 'message' => 'اطلاعات کاربری شما با موفقیت بروز شد' ]);
        }
        return response()->json([ 'status' => 'error' , 'message' => LBL_COMMON_ERROR ]);
    }
    /** ------------------------------------------------------------------------------------------------------------- */

    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(),['cell' => 'required|numeric']);
        if ($validator->fails()) {
            return response()->json(['status' => 'validationError' , 'message' => $validator->errors()]);
        }

        $user = User::where('cell' , $request->input('cell'))->first();
        if ($user){
            $activationCode = mt_rand(11111 , 99999);
            $user->activation_code = $activationCode;
            $user->save();

            //Send Activation Code and return response
            $smsData = [
                'cell' => $user->cell ,
                'text' => $activationCode,
            ];

            // Send the activation code
            SendSingleSMS::dispatch($smsData);
            return response()->json(['status' => 'success' , 'message' => 'SMS has been sent']);
        } else{
            return response()->json(['status' => 'error' , 'message' => LBL_COMMON_ERROR]);
        }
    }
    /** ------------------------------------------------------------------------------------------------------------- */
    public function resetPasswordAction(Request $request)
    {
        $validator = Validator::make($request->all(),['cell' => 'required|numeric' , 'activationCode' =>  'required|numeric' , 'newPassword' => 'required']);
        if ($validator->fails()) {
            return response()->json(['status' => 'validationError' , 'message' => $validator->errors()]);
        }

        $user = User::where('cell' , $request->input('cell'))->first();
        if ($user){
            $received_activation_code = $request->input('activationCode');
            $newPassword = $request->input('newPassword');

            if($user->activation_code == intval($received_activation_code)) {
                $user->update(array('password' => bcrypt($newPassword)));
                return response()->json(['status' => 'success' , 'message' => LBL_COMMON_UPDATE_SUCCESSFUL]);
            } else {
                return response()->json(['status' => 'error' , 'message' => 'کد فعالسازی اشتباه است']);
            }
        } else{
            return response()->json(['status' => 'error' , 'message' => LBL_COMMON_ERROR]);
        }


    }
    /** ------------------------------------------------------------------------------------------------------------- */
    public function addAddress(Request $request)
    {
        $user = auth()->guard('api')->user();

        $validator = Validator::make($request->all() , $this->addressRules);
        if ($validator->fails()) {
            return response()->json(['status' => 'validationError' , 'message' => $validator->errors()]);
        }
        if ( $user ) {

            $title = $request->input('title');
            $address = $request->input('address');
            $city ='تهران';

            $address = new Address(['title' => $title , 'address' => $address , 'city' => $city]);
            $user->addresses()->save($address);

            return response()->json([ 'status' => 'success' , 'message' => 'آدرس با موفقیت ذخیره شد']);
        }

        return response()->json([ 'status' => 'error' , 'message' => LBL_COMMON_ERROR]);

//        return response()->json([ 'success' => $request->all() ]);
    }
    /** ------------------------------------------------------------------------------------------------------------- */
    public function removeAddress(Request $request)
    {
        $id = $request->input('id');
        if($id) {
            $address = Address::find($id)->first();
            if ($address) {
                $address->delete();
                return response()->json([ 'status' => 'success' , 'message' => 'آدرس با موفقیت حذف شد' ]);
            }
            return response()->json([ 'status' => 'error' , 'message' => LBL_COMMON_ERROR ]);
        }
        return response()->json([ 'status' => 'error' , 'message' => LBL_COMMON_ERROR ]);
    }
    /** ------------------------------------------------------------------------------------------------------------- */
    /** ------------------------------------------------------------------------------------------------------------- */
    public function ajaxImageUpload(Request $request)
    {
        $image = $request->input('image');
        $user = auth()->guard('api')->user();

        if($user && $image){
            $extension = getBase64extension($image);
            $convertedImage = Image::make(file_get_contents($image));

            if($user->user_image)
                @$this->imageDelete($user->user_image , $this->destinationPathOfUserAvatars);
            $name = $this->imageUpload($convertedImage , $this->destinationPathOfUserAvatars , null , 300 , $extension);

            if($name) {
                $user->update(array('user_image' => $name));
                return response()->json([ 'status' => 'success' ,  'message' => LBL_COMMON_UPDATE_SUCCESSFUL]);
            } else {
                return response()->json([ 'status' => 'error' ,  'message' => LBL_COMMON_ERROR]);
            }
        }
        return response()->json([ 'status' => 'error' ,  'data' => $request->all()]);
//      return response()->json([ 'status' => 'error' ,  'message' => LBL_COMMON_ERROR]);
    }
    /** -------------------------------------------------------------------------- */
    /** -------------------------------------------------------------------------- */
    protected function imageUpload ($image , $destinationPath , $height = null , $width = 100 , $extension = '.jpg' ){
        $name = time().$extension;

        $image->resize($height, $width, function ($constraint) {
            $constraint->aspectRatio();
        })->save($destinationPath.'/'.$name);

        return $name;
    }
    /** -------------------------------------------------------------------------- */
    protected function imageDelete ($fileName , $destinationPath)
    {
        $fullFileName = $destinationPath . '/' . $fileName;

        if (File::exists($fullFileName)) {
            File::delete($fullFileName);
            return true;
        }
        return false;
    }
    /** -------------------------------------------------------------------------- */
    public function ajaxImageRemove(Request $request)
    {
        $user = auth()->guard('api')->user();
        if($user){
            if($user->user_image) {
                @$this->imageDelete($user->user_image , $this->destinationPathOfUserAvatars);
                $user->update(array('user_image' => null));
                return response()->json([ 'status' => 'success' ,  'message' => LBL_COMMON_UPDATE_SUCCESSFUL]);
            } else {
                return response()->json([ 'status' => 'error' ,  'message' => 'تصویری برای حذف وجود ندارد']);
            }

        }
        return response()->json([ 'status' => 'error' ,  'message' => LBL_COMMON_ERROR]);

//        return response()->json([ 'status' => 'error' ,  'data' => $request->all()]);
    }

}
