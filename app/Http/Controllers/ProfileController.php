<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function update(UpdateUserRequest $request)
    {
        $user = auth()->user();
        $user->update($request->validated());
        $user = $user->fresh();

        return jsonResponse($user);
    }
}