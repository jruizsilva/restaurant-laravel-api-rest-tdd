<?php

namespace Tests\Feature;

use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(UserSeeder::class);
    }

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
        $response->assertJsonFragment([
            'data' => [
                ...$responseData,
                'id' => 2,
                'email' => 'email@email.com',
                'name' => 'Name 1',
                'last_name' => 'Last Name 1',
            ],
        ]);
        $this->assertDatabaseCount('users', 2);
        $this->assertDatabaseHas('users', [
            'email' => 'email@email.com',
            'name' => 'Name 1',
            'last_name' => 'Last Name 1',
        ]);
    }

    #[Test]
    public function a_user_can_register_and_login(): void
    {
        $credentials = [
            'email' => 'user@user.com',
            'password' => 'password',
            'name' => 'Name 2',
            'last_name' => 'Last Name 2',
        ];

        $response = $this->postJson('api/v1/users', $credentials);
        $response->assertStatus(201);
        $this->assertDatabaseCount('users', 2);
        $response = $this->postJson('api/v1/login', [
            'email' => $credentials['email'],
            'password' => $credentials['password'],
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure(['data' => ['token']]);
    }

    #[Test]
    public function a_user_cannot_register_with_an_existing_email(): void
    {
        $credentials = [
            'email' => 'example@example.com',
            'password' => 'password',
            'name' => 'Name 1',
            'last_name' => 'Last Name 1',
        ];

        $response = $this->postJson('api/v1/users', $credentials);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors('email');
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

    #[Test]
    public function email_must_be_unique(): void
    {
        # teniendo
        $data = [
            'email' => 'example@example.com',
            'password' => 'password',
            'name' => 'example',
            'last_name' => 'example example',
        ];

        # haciendo
        $response = $this->postJson("api/v1/users", $data);

        #esperando
        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'data', 'status', 'errors' => ['email']]);
        $response->assertJsonFragment(['errors' => ['email' => ['The email has already been taken.']]]);
    }

    #[Test]
    public function name_must_be_at_least_2_characters()
    {
        $credentials = [
            'email' => 'abc@gmail.com',
            'password' => '1234',
            'name' => '1'
        ];

        $response = $this->postJson('/api/v1/users', $credentials);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('name');
    }

    #[Test]
    public function last_name_must_be_at_least_2_characters(): void
    {
        $credentials = [
            'email' => 'abc@gmail.com',
            'password' => '1234',
            'name' => 'Test',
            'last_name' => '1'
        ];
        $response = $this->postJson('/api/v1/users', $credentials);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors('last_name');
    }
}