<?php

namespace Tests\Feature;

use App\Models\User;
use App\Notifications\ResetPasswordNotification;
use Database\Seeders\UserSeeder;
use Hash;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Notification;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ResetPasswordTest extends TestCase
{
    use RefreshDatabase;
    protected $token;
    protected $email;

    public function setUp(): void
    {
        parent::setUp();
        $this->seed(UserSeeder::class);
    }

    private function sendResetPasswordEmailAndSetAttributes()
    {
        Notification::fake();
        $data = ['email' => 'example@example.com'];
        $response = $this->postJson('api/v1/forgot-password', $data);
        $response->assertStatus(200);
        $user = User::where('email', $data['email'])->first();
        Notification::assertSentTo(
            $user,
            ResetPasswordNotification::class,
            function (ResetPasswordNotification $notification) {
                $url = $notification->url;
                $parts = parse_url($url);
                parse_str($parts['query'], $query);
                $this->token = $query['token'];
                $this->email = $query['email'];
                return str_contains($url, "http://frontendurl.com/reset-password?token=");
            }
        );
        return $user;
    }

    #[Test]
    public function email_must_exist_to_reset_password(): void
    {
        $data = ['email' => 'notexisting@example.com'];
        $response = $this->postJson('api/v1/forgot-password', $data);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors('email');
    }

    #[Test]
    public function password_must_be_change(): void
    {
        $user = $this->sendResetPasswordEmailAndSetAttributes();
        $data = ['password' => 'newpassword', 'password_confirmation' => 'newpassword'];
        $response = $this->putJson("api/v1/reset-password?token=$this->token&email=$this->email", $data);
        $response->assertStatus(200);
        $user->refresh();
        $this->assertTrue(Hash::check("newpassword", $user->password));
    }

    #[Test]
    public function email_is_required(): void
    {
        $this->sendResetPasswordEmailAndSetAttributes();
        $data = [
            'password' => 'newpassword',
            'password_confirmation' => 'newpassword'
        ];
        $response = $this->putJson("api/v1/reset-password?token=$this->token&email=", $data);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors('email');
    }

    #[Test]
    public function token_is_required(): void
    {
        $this->sendResetPasswordEmailAndSetAttributes();
        $data = [
            'password' => 'newpassword',
            'password_confirmation' => 'newpassword'
        ];
        $response = $this->putJson("api/v1/reset-password?token=&email=$this->email", $data);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors('token');
    }

    #[Test]
    public function password_is_required(): void
    {
        $this->sendResetPasswordEmailAndSetAttributes();
        $data = [
            'password' => '',
            'password_confirmation' => 'newpassword'
        ];
        $response = $this->putJson("api/v1/reset-password?token=$this->token&email=$this->email", $data);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors('password');
    }

    #[Test]
    public function password_confirmation_is_required(): void
    {
        $this->sendResetPasswordEmailAndSetAttributes();
        $data = [
            'password' => 'newpassword',
            'password_confirmation' => ''
        ];
        $response = $this->putJson("api/v1/reset-password?token=$this->token&email=$this->email", $data);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors('password_confirmation');
    }

    #[Test]
    public function password_must_be_at_least_4_characters_to_reset_password(): void
    {
        $this->sendResetPasswordEmailAndSetAttributes();
        $data = [
            'password' => 'new',
            'password_confirmation' => 'newpass'
        ];
        $response = $this->putJson("api/v1/reset-password?token=$this->token&email=$this->email", $data);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors('password');
    }

    #[Test]
    public function password_confirmation_must_be_at_least_4_characters_to_reset_password(): void
    {
        $this->sendResetPasswordEmailAndSetAttributes();
        $data = [
            'password' => 'newpassword',
            'password_confirmation' => 'new'
        ];
        $response = $this->putJson("api/v1/reset-password?token=$this->token&email=$this->email", $data);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors('password_confirmation');
    }

    #[Test]
    public function password_confirmation_must_be_equal_to_password(): void
    {
        $this->sendResetPasswordEmailAndSetAttributes();
        $data = [
            'password' => 'newpassword',
            'password_confirmation' => 'wrongpassword'
        ];
        $response = $this->putJson("api/v1/reset-password?token=$this->token&email=$this->email", $data);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors('password_confirmation');
    }

    #[Test]
    public function token_must_be_valid_to_reset_password(): void
    {
        $this->sendResetPasswordEmailAndSetAttributes();
        $data = [
            'password' => 'newpassword',
            'password_confirmation' => 'newpassword'
        ];
        $response = $this->putJson("api/v1/reset-password?token=wrongtoken&email=$this->email", $data);
        $response->assertStatus(403);
    }
}