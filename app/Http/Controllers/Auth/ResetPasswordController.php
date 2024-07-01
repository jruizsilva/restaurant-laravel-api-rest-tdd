<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

class ResetPasswordController extends Controller
{
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        $statusCode = match ($status) {
            Password::RESET_LINK_SENT => 200,
            Password::INVALID_USER => 404,
            default => 200,
        };
        if ($statusCode === 404) {
            return jsonResponse([], $statusCode, trans($status));
        }
        return jsonResponse([], $statusCode, trans($status));
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:4',
            'password_confirmation' => 'required|min:4|same:password'
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => bcrypt($password)
                ])->save();
            }
        );

        $statusCode = match ($status) {
            Password::PASSWORD_RESET => 200,
            Password::INVALID_TOKEN => 403,
            Password::INVALID_USER => 404,
            default => 400,
        };

        if ($statusCode === 404) {
            return jsonResponse([], $statusCode, trans($status));
        }

        return jsonResponse([], $statusCode, trans($status));
    }
}