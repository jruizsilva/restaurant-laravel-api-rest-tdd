<?php

namespace Tests\Feature;

use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(UserSeeder::class);
    }
    #[Test]
    public function an_existing_user_can_login(): void
    {
        $credentials = [
            'email' => 'test@test.com',
            'password' => 'password'
        ];
        $response = $this->postJson("api/v1/login", $credentials);
        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => ['token', 'token_type', 'expires_in'],
                'status',
                'message',
                'errors'
            ]);
    }

    #[Test]
    public function a_non_existing_user_cannot_login(): void
    {
        $credentials = [
            'email' => 'test@notexisting.com',
            'password' => 'password'
        ];

        $response = $this->postJson('/api/v1/login', $credentials);

        $response->assertStatus(status: 401)
            ->assertJsonStructure([
                'data',
                'status',
                'message',
                'errors'
            ]);
    }

    #[Test]
    public function email_must_be_required(): void
    {
        $credentials = [
            'password' => 'password'
        ];

        $response = $this->postJson('/api/v1/login', $credentials);

        $response->assertStatus(422)
            ->assertJsonStructure([
                'data',
                'status',
                'message',
                'errors' => ['email']
            ]);
    }

    #[Test]
    public function password_must_be_required(): void
    {
        $credentials = [
            'email' => 'test@notexisting.com',
        ];

        $response = $this->postJson('/api/v1/login', $credentials);

        $response->assertStatus(422)
            ->assertJsonStructure([
                'data',
                'status',
                'message',
                'errors' => ['password']
            ]);
    }
}