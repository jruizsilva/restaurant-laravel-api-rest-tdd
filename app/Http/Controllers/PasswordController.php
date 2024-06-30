<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdatePasswordRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PasswordController extends Controller
{
    public function update(UpdatePasswordRequest $request)
    {
        Auth::user()->update([
            'password' => bcrypt($request->password)
        ]);

        return jsonResponse();
    }
}