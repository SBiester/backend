<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('microsoft')->redirect();
    }

    public function callback()
    {
        try {
            $microsoftUser = Socialite::driver('microsoft')->user();

            $user = User::updateOrCreate([
                'email' => $microsoftUser->getEmail(),
            ], [
                'name' => $microsoftUser->getName(),
                'microsoft_id' => $microsoftUser->getId(),
                'password' => bcrypt(str()->random(16))
            ]);

            Auth::login($user);

            return redirect('http://localhost:5173');

        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return redirect('http://localhost:5173/login?error=1');
        }
    }

    public function user(Request $request)
    {
        return $request->user();
    }

    public function logout(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json(['message' => 'Logged out']);
    }
}
