<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdatePasswordRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PasswordController extends Controller
{
    public function update(UpdatePasswordRequest $request)
    {
        $user = Auth::user();
        $user->update([
            'password' => bcrypt($request->password)
        ]);
        $user->refresh();

        return jsonResponse($user);
    }
}