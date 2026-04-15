<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        if (auth('sanctum')->check()) {
            return response()->noContent();
        }

        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
            'device_name' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! Hash::check($request->password, optional($user)->password)) {
            throw ValidationException::withMessages([
                'email' => [__('auth.failed')],
            ]);
        }

        return response()->json([
            'plain-text-token' => $user->createToken($request->device_name)->plainTextToken,
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->noContent();
    }
}
