<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_visitor_can_register_with_valid_data()
    {
        $response = $this->post('/register', [
            'full_name'             => 'Иван Иванов Петрович',
            'email'                 => 'test@example.com',
            'phone'                 => '+71234567890',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect(route('home'));
        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
            'role'  => 'visitor',
        ]);
    }

    public function test_registration_fails_with_invalid_email()
    {
        $response = $this->post('/register', [
            'full_name'             => 'Иван Иванов',
            'email'                 => 'not-an-email',
            'phone'                 => '+71234567890',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_registration_fails_with_invalid_phone()
    {
        $response = $this->post('/register', [
            'full_name'             => 'Иван Иванов',
            'email'                 => 'test@example.com',
            'phone'                 => '123',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors('phone');
    }

    public function test_registration_fails_if_email_already_exists()
    {
        User::factory()->create(['email' => 'test@example.com']);

        $response = $this->post('/register', [
            'full_name'             => 'Петр Петров',
            'email'                 => 'test@example.com',
            'phone'                 => '+71234567891',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_user_can_login_with_correct_credentials()
    {
        $user = User::factory()->create([
            'email'    => 'test@example.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->post('/login', [
            'email'    => 'test@example.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('home'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_user_cannot_login_with_wrong_password()
    {
        User::factory()->create([
            'email'    => 'test@example.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->post('/login', [
            'email'    => 'test@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_master_is_redirected_to_cabinet_after_login()
    {
        $master = User::factory()->master()->create([
            'email'    => 'master@example.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->post('/login', [
            'email'    => 'master@example.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('cabinet'));
    }

    public function test_visitor_is_redirected_to_home_after_login()
    {
        $visitor = User::factory()->create([
            'email'    => 'visitor@example.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->post('/login', [
            'email'    => 'visitor@example.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('home'));
    }
}
