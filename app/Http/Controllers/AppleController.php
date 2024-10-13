<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
    } catch (\Exception $e) {
        return redirect()->route('login')->with('error', 'Failed to log in with Apple.');
    }

    $provider_id = $user->getId();
    $email = $user->getEmail() ?? 'no-email-provided';

    $existingUser = User::where('social_id', $provider_id)->orWhere('email', $email)->first();

    if ($existingUser) {
        Auth::login($existingUser);
    } else {
        $newUser = User::create([
            'name' => $user->getName() ?? 'Apple User',
            'email' => $email,
            'social_id' => $provider_id,
            'social_type' => 'apple',
            'password' => bcrypt('my-apple')
        ]);

        Auth::login($newUser);
    }

    // If it's a mobile app, generate and redirect with a token
    $token = Auth::guard('user-api')->user();

    return $token;
 }
}
