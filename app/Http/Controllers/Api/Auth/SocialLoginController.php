<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\User;

use Illuminate\Http\Request;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Socialite;


class SocialLoginController extends Controller
{
    public function googleLogin(Request $request)
    {
        $gToken = $request->input('gToken');
        $provider = $request->input('provider');

        if($gToken){
            $user = Socialite::driver('google')->userFromToken($gToken);
            if($user) {
                $count = User::whereEmail($user->email)->count();
                if($count) {
                    $localUser = User::whereEmail($user->email)->first();
                } else {
                    $userData = array(
                        'email' => $user->email,
                        'user_image' => $user->avatar_original,
                        'password' => bcrypt($user->id),
                        'is_confirm' => 'y',
                        'provider' => $provider ? $provider : null
                    );
                    $localUser = User::create($userData);
                }
                $token = JWTAuth::fromUser($localUser);
                return response()->json(['status' => 1 , 'token' => $token ]);
            }
            return response()->json(['status' => 2 , 'message' => 'No user From google' ]);
        }
        return response()->json(['status' => 2 , 'message' => 'No Gtoken provided' ]);
    }

}
