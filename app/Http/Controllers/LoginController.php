<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:4'
        ]);
        $credentials = $request->only('email', 'password');

        if (!$token = auth()->attempt($credentials)) {
            return jsonResponse(status: 401, message: 'Credenciales incorrectas');
        }

        // return $this->respondWithToken($token);

        return jsonResponse(data: [
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => JWTAuth::factory()->getTTL() * 60
        ]);
    }
}