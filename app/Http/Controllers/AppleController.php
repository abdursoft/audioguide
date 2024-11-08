<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Auth\JWTAuth;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;

class AppleController extends Controller
{
    public function redirectToProvider()
    {
        Log::info("apple");
        return Socialite::driver('apple')->redirect();
    }

    public function handleProviderCallback()
    {
        try {
            $user = Socialite::driver('apple')->user();

            $provider_id = $user->getId();
            $email = $user->getEmail() ?? 'no-email-provided';

            $existingUser = User::where('social_id', $provider_id)->orWhere('email', $email)->first();

            if ($existingUser) {
                $token = JWTAuth::createToken('user_token', 8740, $existingUser->id, $existingUser->email);
            } else {
                $newUser = User::create([
                    'name' => $user->getName() ?? 'Apple User',
                    'email' => $email,
                    'social_id' => $provider_id,
                    'social_type' => 'apple',
                    'password' => bcrypt('my-apple'),
                ]);

                $token = JWTAuth::createToken('user_token', 8740, $newUser->id, $email);
            }
            return redirect()->away(env('FRONT_END').'auth?token=' . $token);
        } catch (\Exception $e) {
            return redirect()->away(env('FRONT_END'));
        }
    }
}
