<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        try {
            $request->validate([
                'email' => 'email|required',
                'password' => 'required'
            ]);

            $credentials = request(['email', 'password']);

            if (!Auth::attempt($credentials)) {
                return response()->json([
                    'error'=> true,
                    'message' => 'Unauthorized',
                    'data' => null
                ], 500);
            }

            $user = User::where('email', $request->email)->first();

            if (!Hash::check($request->password, $user->password, [])) {
                throw new \Exception('Error in Login');
            }

            $tokenResult = $user->createToken('authToken')->plainTextToken;

            return response()->json([
                'success'=> true,
                'message' => 'Logged in successfully',
                'access_token' => $tokenResult,
                'token_type' => 'Bearer',
            ], 200);
        } catch (\Exception $error) {
            return response()->json([
                'error'=> true,
                'message' => 'Error in Login',
                'data' => $error
            ], 500);
        }
    }
}

