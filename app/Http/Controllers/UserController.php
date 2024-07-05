<?php

namespace App\Http\Controllers;

use App\Enums\Roles;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function destroy(User $user)
    {
        if ($user->id === auth()->id())
            return jsonResponse(message: 'You cannot delete your own account', status: 401);

        if (in_array(Roles::OWNER->name, $user->getRoleNames()->toArray())) {
            return jsonResponse(message: 'You cannot delete an owner account', status: 401);
        }
        $user->delete();
        return jsonResponse();
    }
}