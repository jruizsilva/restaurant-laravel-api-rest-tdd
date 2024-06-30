<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\UserSeeder;
use Hash;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class UpdatePasswordTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(UserSeeder::class);
    }

    #[Test]
    public function user_password_can_change(): void
    {
        $data = [
            'current_password' => 'password',
            'password' => 'newpassword',
            'password_confirmation' => 'newpassword'
        ];

        $response = $this->apiAs(User::find(1), 'put', '/api/v1/password', $data);
        $response->assertStatus(200);
        $this->assertTrue(Hash::check('newpassword', auth()->user()->password));
    }

    // #[Test]
    // public function user_password_cannot_change_with_wrong_current_password(): void
    // {
    //     $data = [
    //         'current_password' => 'wrongpassword',
    //         'password' => 'newpassword',
    //         'password_confirmation' => 'newpassword'
    //     ];

    //     $response = $this->apiAs(User::find(1), 'put', '/api/v1/password', $data);
    //     $response->assertStatus(422);
    //     $response->assertJsonValidationErrors('current_password');
    // }

    #[Test]
    public function user_password_cannot_change_with_wrong_password_confirmation(): void
    {
        $data = [
            'current_password' => 'password',
            'password' => 'newpassword',
            'password_confirmation' => 'wrongpassword'
        ];

        $response = $this->apiAs(User::find(1), 'put', '/api/v1/password', $data);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors('password_confirmation');
    }

    #[Test]
    public function current_password_is_required(): void
    {
        $data = [
            'password' => 'newpassword',
            'password_confirmation' => 'newpassword'
        ];

        $response = $this->apiAs(User::find(1), 'put', '/api/v1/password', $data);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors('current_password');
    }

    #[Test]
    public function password_is_required(): void
    {
        $data = [
            'current_password' => 'password',
            'password_confirmation' => 'newpassword'
        ];

        $response = $this->apiAs(User::find(1), 'put', '/api/v1/password', $data);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors('password');
    }

    #[Test]
    public function password_confirmation_is_required(): void
    {
        $data = [
            'current_password' => 'password',
            'password' => 'newpassword'
        ];

        $response = $this->apiAs(User::find(1), 'put', '/api/v1/password', $data);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors('password_confirmation');
    }

    #[Test]
    public function password_must_has_minimum_4_characters(): void
    {
        $data = [
            'current_password' => 'password',
            'password' => 'new',
            'password_confirmation' => 'new'
        ];

        $response = $this->apiAs(User::find(1), 'put', '/api/v1/password', $data);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors('password');
    }

    #[Test]
    public function current_password_must_has_minimum_4_characters(): void
    {
        $data = [
            'current_password' => 'pa',
            'password' => 'newpassword',
            'password_confirmation' => 'newpassword'
        ];

        $response = $this->apiAs(User::find(1), 'put', '/api/v1/password', $data);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors('current_password');
    }
}