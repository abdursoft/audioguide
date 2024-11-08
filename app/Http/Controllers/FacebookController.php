<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Auth\JWTAuth;
use App\Models\User;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;

class FacebookController extends Controller
{
    public function redirectToFacebook()
    {
        return Socialite::driver('facebook')->redirect();
    }

    public function handleFacebookCallback()
    {
        try {
            $user = Socialite::driver('facebook')->user();
            $findUser = User::where('facebook_id', $user->id)->first();

            if ($findUser) {
                $token = JWTAuth::createToken('user_token',8740,$findUser->id,$findUser->email);
            } else {
                $newUser = User::create([
                    'name' => $user->name,
                    'email' => $user->email,
                    'social_id'=> $user->id,
                    'social_type' => 'facebook',
                    'password' => encrypt('my-facebook')
                ]);

                $token = JWTAuth::createToken('user_token',8740,$newUser->id,$user->email);
                return redirect()->away(env('FRONT_END').'auth?token='.$token);
            }
        } catch (\Exception $e) {
            return redirect()->away(env('FRONT_END'));
        }
    }
}
