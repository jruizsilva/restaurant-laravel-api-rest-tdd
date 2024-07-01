<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;

use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;

class RegisterController extends Controller
{
    public function store(RegisterRequest $request)
    {
        $data = $request->validated();
        $data["password"] = bcrypt($data["password"]);
        $user = User::create($data);

        return jsonResponse($user, 201, "User created");
    }
}