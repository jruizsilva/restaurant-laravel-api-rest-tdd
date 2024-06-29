<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class RegisterController extends Controller
{
    public function store(Request $request)
    {
        $user = User::create($request->all());

        return jsonResponse($user, 201, "User created");
    }
}