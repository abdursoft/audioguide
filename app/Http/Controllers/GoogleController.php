<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Auth\JWTAuth;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
    /**
     * Google redirect
     */
    public function redirectToGoogle(Request $request)
    {
        $request->session()->regenerate();
        return Socialite::driver('google')->redirect();
    }

    /**
     * Google authentication
     */
    public function handleGoogleCallback()
    {
        try {
            $user = Socialite::driver('google')->user();
            $finduser = User::where('social_id', $user->id)->first();

            if ($finduser){
                $token = JWTAuth::createToken('user_token',8740,$finduser->id,$finduser->email);
            }
            else
            {
                $newUser = User::create([
                    'name' => $user->name,
                    'email' => $user->email,
                    'social_id' => $user->id,
                    'social_type' => 'google',
                    'password' => bcrypt('my-google'),
                ]);

                $token = JWTAuth::createToken('user_token',8740,$newUser->id,$user->email);
            }
            return redirect()->away(env('FRON_END'.'auth?token='.$token));
        }
        catch (Exception $e)
        {
            return redirect()->away(env('FRONT_END'));
        }
    }
}
