<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use Tests\TestCase;

class AdminAccessTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Teste que les utilisateurs non authentifiés sont redirigés depuis le tableau de bord admin.
     *
     * @return void
     */
    public function test_guest_is_redirected_from_admin_dashboard()
    {
        $response = $this->get('/admin/dashboard');

        $response->assertRedirect('/login');
    }

    /**
     * Teste que les utilisateurs non-administrateurs ne peuvent pas accéder au tableau de bord admin.
     *
     * @return void
     */
    public function test_non_admin_user_cannot_access_admin_dashboard()
    {
        $user = User::factory()->create(['role' => 'user']);
        $this->actingAs($user);

        $response = $this->get('/admin/dashboard');

        $response->assertRedirect('/');
    }

    /**
     * Teste que les administrateurs peuvent accéder au tableau de bord admin.
     *
     * @return void
     */
    public function test_admin_user_can_access_admin_dashboard()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        $response = $this->get('/admin/dashboard');

        $response->assertStatus(200);
        $response->assertViewIs('admin.dashboard');
    }
}
