<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class UpdateUserDataTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $this->seed(UserSeeder::class);
    }
    #[Test]
    public function an_authenticated_user_can_modify_their_data(): void
    {
        $data = [
            'name' => 'Name changed',
            'last_name' => 'Last Name changed'
        ];
        $response = $this->apiAs(User::find(1), 'put', 'api/v1/profile', $data);
        $responseData = $response->json('data');
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'data' => [
                ...$responseData,
                'name' => 'Name changed',
                'last_name' => 'Last Name changed',
            ]
        ]);
        
        $this->assertDatabaseHas('users', [
            'name' => 'Name changed',
            'last_name' => 'Last Name changed'
        ]);
    }
    #[Test]
    public function an_authenticated_user_cannot_modify_their_email(): void
    {
        $data = [
            'email' => 'newemail@gmail.com',
            'name' => 'Example',
            'last_name' => 'Example',
        ];
        $response = $this->apiAs(User::find(1), 'put', 'api/v1/profile', $data);
        $response->assertStatus(200);
        $this->assertDatabaseMissing('users', [
            'email' => 'newemail@gmail.com'
        ]);
    }
    #[Test]
    public function an_authenticated_user_cannot_modify_their_password(): void{
        $data = [
            'password' => 'newpassword',
            'name' => 'Example',
            'last_name' => 'Example',
        ];
        $user = User::find(1);
        $response = $this->apiAs($user, 'put', 'api/v1/profile', $data);
        $response->assertStatus(200);
        $this->assertFalse(Hash::check('newpassword', $user->password));
    }

    #[Test]
    public function field_name_is_required()
    {
        $data = [
            'last_name' => 'Last Name 1',
        ];
        $response = $this->apiAs(User::find(1), 'put', 'api/v1/profile', $data);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors('name');
    }

    #[Test]
    public function field_last_name_is_required()
    {
        $data = [
            'name' => 'Name 1',
        ];
        $response = $this->apiAs(User::find(1), 'put', 'api/v1/profile', $data);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors('last_name');
    }

    #[Test]
    public function name_must_be_at_least_2_characters()
    {
        $data = [
            'last_name' => 'last name',
            'name' => '1'
        ];

        $response = $this->apiAs(User::find(1), 'put', 'api/v1/profile', $data);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('name');
    }

    #[Test]
    public function last_name_must_be_at_least_2_characters(): void
    {
        $data = [
            'name' => 'Test',
            'last_name' => '1'
        ];
        $response = $this->apiAs(User::find(1), 'put', 'api/v1/profile', $data);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors('last_name');
    }
}