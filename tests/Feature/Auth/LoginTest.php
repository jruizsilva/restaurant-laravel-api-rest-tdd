<?php

namespace Tests\Feature\Auth;

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
            'email' => 'example@example.com',
            'password' => 'password'
        ];
        $response = $this->postJson("api/v1/login", $credentials);
        $response->assertStatus(200);
        $response->assertJsonStructure(['data' => ['token']]);
    }

    #[Test]
    public function a_non_existing_user_cannot_login(): void
    {
        $credentials = [
            'email' => 'test@notexisting.com',
            'password' => 'password'
        ];

        $response = $this->postJson('/api/v1/login', $credentials);

        $response->assertStatus(status: 401);
        $response->assertJsonStructure(['data', 'status', 'message', 'errors']);
    }

    #[Test]
    public function email_must_be_required(): void
    {
        $credentials = [
            'password' => 'password'
        ];

        $response = $this->postJson('/api/v1/login', $credentials);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('email');
    }

    #[Test]
    public function password_must_be_required(): void
    {
        $credentials = [
            'email' => 'test@notexisting.com',
        ];

        $response = $this->postJson('/api/v1/login', $credentials);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('password');
    }

    #[Test]
    public function email_must_be_valid_email(): void
    {
        $credentials = [
            'email' => 'test',
            'password' => 'password'
        ];

        $response = $this->postJson('/api/v1/login', $credentials);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('email');
    }

    #[Test]
    public function password_must_be_at_least_4_characters(): void
    {
        $credentials = [
            'email' => 'email@gmail.com',
            'password' => '12'
        ];

        $response = $this->postJson('/api/v1/login', $credentials);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('password');
    }
}