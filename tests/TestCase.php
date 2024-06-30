<?php

namespace Tests;

use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

abstract class TestCase extends BaseTestCase
{
    protected function apiAs(User $user, $method, $uri, $data)
    {
        $headers = [
            "Authorization" => "Bearer " . JWTAuth::fromUser($user)
        ];

        return $this->json($method, $uri, $data, $headers);
    }
}