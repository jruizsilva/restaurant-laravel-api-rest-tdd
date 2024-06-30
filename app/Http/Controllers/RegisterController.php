<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;

class RegisterController extends Controller
{
    public function store(RegisterRequest $request)
    {
        $user = User::create($request->validated());

        return jsonResponse($user, 201, "User created");
    }
}