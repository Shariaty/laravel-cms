<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;


class LoginController extends Controller
{
    /** ------------------------------------------------------------------------------------------------------------- */
    protected function validateLogin(Request $request)
    {
        $this->validate($request, [
            'cell' => 'required|numeric',
            'password' => 'required'
        ]);
    }
    /** ------------------------------------------------------------------------------------------------------------- */
    public function numberReceive(Request $request)
    {
        $this->validate($request, [
            'cell' => 'required|numeric'
        ]);

        $cell = $request->input('cell');
        $user = User::where('cell' , $cell)->first();

        if(count($user)) {
            if(!$user->password && !$user->fullName) {
                return response()->json(['isRegistered' => true , 'status' => $user->status  , 'incomplete' => true ]);
            }
            return response()->json(['isRegistered' => true ,  'status' => $user->status , 'incomplete' => false ]);
        } else {
            $result =  $this->_newUserSituation($request);
            return response()->json(['isRegistered' => false , 'result' => $result]);
        }
    }
    /** ------------------------------------------------------------------------------------------------------------- */
    public function activateAccount(Request $request)
    {
        $this->validate($request, [
            'cell'  => 'required|numeric',
            'activation_code' => 'required|numeric|digits:5'
        ]);

        $received_activation_code = $request->input('activation_code');
        $cell = $request->input('cell');

        $user = User::where('cell' , $cell)->first();

        if(count($user)) {
            if($user->activation_code == intval($received_activation_code)) {
                $user->update(array('status' => PUS_ACTIVE));
                return response()->json(['result' => 'User has been activated']);
            } else {
                return response()->json(['result' => 'Activation code is incorrect']);
            }
        } else {
            return response()->json(['result' => 'There is no user based on your inputs']);
        }
    }
    /** ------------------------------------------------------------------------------------------------------------- */
    public function register(Request $request)
    {
        $this->validate($request, [
            'cell'  => 'required|numeric',
            'fullName' => 'required|max:100',
            'password' => 'required|min:6'
        ]);

        $cell = $request->input('cell');
        $user = User::where('cell' , $cell)->first();

        if(count($user)) {
            $user->update(array(
                            'fullName' => $request->input('fullName') ,
                            'password' => bcrypt($request->input('password'))
            ));

            //Login the user
            $token = auth()->guard('api')->login($user);
            @$this->_updateLastLogin($token);
            return response()->json(compact('token') , 200);
        }
        return response()->json(['result' => 'There is no user based on your inputs']);

    }
    /** ------------------------------------------------------------------------------------------------------------- */
    public function login(Request $request)
    {
        $this->validateLogin($request);
        $credentials = ['cell' => $request->cell , 'password' => $request->password ];
        try {
            $token = auth()->guard('api')->attempt($credentials);

            if ($token) {
                $user = $this->_retrieveUserFromToken($token);
                switch ($user->status) {
                    case PUS_ACTIVE :
                        // all good so return the token
                        @$this->_updateLastLogin($token);
                        return response()->json(compact('token') , 200);
                        break;
                    case PUS_DISABLED :
                        return response()->json(['status' => 'error' , 'message' => 'اکانت شما غیر فعال شده است ، جهت پیگیری با مدیریت سایت در ارتباط باشید'], 260);
                        break;
                    case PUS_WAIT_FOR_CONFIRM :
                        return response()->json(['status' => 'error' , 'message' => 'اکانت شما هنوز فعال نشده است ، لطفا نسبت به فعالسازی آن اقدام نمایید'], 270);
                        break;
                    default :
                        return response()->json(['status' => 'error' , 'message' => LBL_COMMON_ERROR ], 280);
                }
            } else {
                return response()->json(['status' => 'error' , 'message' => 'کاربری با مشخصات ارسالی وجود ندارد'], 250);
            }
        } catch (JWTException $e) {
            // something went wrong whilst attempting to encode the token
            return response()->json(['status' => 'error' , 'message' => 'could_not_create_token'], 500);
        }
     }
    /** ------------------------------------------------------------------------------------------------------------- */
    public function checkUser(Request $request)
    {
        $result = [
            'isLogin' => false ,
            'token' => null ,
            'userInfo' => []
        ];
        $user = auth()->guard('api')->user();
        if($user) {
            $result['isLogin'] = true;
            $result['isLoaded'] = true;
            $result['token'] = $request->input('token');
            $result['userInfo'] = [
                'id' => $user->id,
                'name' => $user->fullName,
                'cell' => $user->cell ,
                'email' => $user->email ,
                'status' => $user->status ,
                'avatar' => $user->avatar ,
                'addresses' => $user->addresses
            ];
        }

        return response()->json( $result  , 200);
    }
    /** ------------------------------------------------------------------------------------------------------------- */


    /** ------------------------------------------------------------------------------------------------------------- */
    protected function _newUserSituation(Request $request) {

        $activationCode = mt_rand(11111 , 99999);
        //Create user and send activation code
        $user = User::create([
            'cell'              => $request->input('cell'),
            'activation_code'   => $activationCode
        ]);

        //Send Activation Code and return response
        return [ 'activationSent' => true ];
    }
    /** ------------------------------------------------------------------------------------------------------------- */
    protected function _updateLastLogin($token) {

        //Get user from token and update last login
        $user = $this->_retrieveUserFromToken($token);
        if($user) {
            $user->update(array('last_login' => Carbon::now()));
            return true;
        }
        return false;
    }
    /** ------------------------------------------------------------------------------------------------------------- */
    protected function _retrieveUserFromToken($token){
        $user = auth()->guard('api')->setToken($token)->user();
        return $user ? $user : false;
    }
    /** ------------------------------------------------------------------------------------------------------------- */

}
