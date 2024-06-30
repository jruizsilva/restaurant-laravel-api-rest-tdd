<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;
    #[Test]
    public function a_user_can_register(): void
    {
        $credentials = [
            'email' => 'email@email.com',
            'password' => 'password',
            'name' => 'Name 1',
            'last_name' => 'Last Name 1',
        ];

        $response = $this->postJson('api/v1/users', $credentials);
        $responseData = $response->json('data');

        $response->assertStatus(201);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'email',
                'name',
                'last_name',
            ],
        ]);
        $response->assertJsonFragment([
            'data' => [
                'id' => 1,
                'email' => 'email@email.com',
                'name' => 'Name 1',
                'last_name' => 'Last Name 1',
                'created_at' => $responseData['created_at'],
                'updated_at' => $responseData['updated_at'],
            ],
        ]);
        $this->assertDatabaseCount('users', 1);
        $this->assertDatabaseHas('users', [
            'email' => 'email@email.com',
            'name' => 'Name 1',
            'last_name' => 'Last Name 1',
        ]);
    }

    #[Test]
    public function field_name_is_required()
    {
        $credentials = [
            'email' => 'email@gmail.com',
            'password' => 'password',
            'last_name' => 'Last Name 1',
        ];
        $response = $this->postJson('api/v1/users', $credentials);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors('name');
    }

    #[Test]
    public function field_last_name_is_required()
    {
        $credentials = [
            'email' => 'email@gmail.com',
            'name' => 'Name 1',
            'password' => 'password',
        ];
        $response = $this->postJson('api/v1/users', $credentials);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors('last_name');
    }

    #[Test]
    public function field_email_is_required()
    {
        $credentials = [
            'name' => 'Name 1',
            'last_name' => 'Last Name 1',
            'password' => 'password',
        ];
        $response = $this->postJson('api/v1/users', $credentials);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors('email');
    }

    #[Test]
    public function field_password_is_required()
    {
        $credentials = [
            'email' => 'email@gmail.com',
            'name' => 'Name 1',
            'last_name' => 'Last Name 1',
        ];
        $response = $this->postJson('api/v1/users', $credentials);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors('password');
    }

    #[Test]
    public function email_must_be_valid()
    {
        $credentials = [
            'email' => 'invalid-email',
            'name' => 'Name 1',
            'last_name' => 'Last Name 1',
            'password' => 'password',
        ];
        $response = $this->postJson('api/v1/users', $credentials);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors('email');
    }
}