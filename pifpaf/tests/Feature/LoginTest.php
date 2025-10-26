<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test for user login.
     */
    #[Test]
    public function a_user_can_login(): void
    {
        // 1. Create a user
        $user = User::factory()->create([
            'password' => bcrypt($password = 'i-love-laravel'),
        ]);

        // 2. Send a POST request to the login route
        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => $password,
        ]);

        // 3. Assert the user is authenticated
        $this->assertAuthenticatedAs($user);

        // 4. Assert the user is redirected to the dashboard
        $response->assertRedirect('/dashboard');
    }

    #[Test]
    public function a_user_cannot_login_with_an_invalid_password(): void
    {
        // 1. Create a user
        $user = User::factory()->create([
            'password' => bcrypt('i-love-laravel'),
        ]);

        // 2. Send a POST request to the login route with an invalid password
        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        // 3. Assert the user is not authenticated
        $this->assertGuest();

        // 4. Assert the session has an error
        $response->assertSessionHasErrors('email');
    }
}
