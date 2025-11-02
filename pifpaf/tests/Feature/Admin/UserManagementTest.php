<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->user = User::factory()->create(['role' => 'user']);
    }

    /** @test */
    public function only_admins_can_access_the_user_management_page()
    {
        // Un non-administrateur reçoit une erreur 403
        $this->actingAs($this->user)
             ->get(route('admin.users.index'))
             ->assertStatus(403);

        // Un administrateur peut y accéder
        $this->actingAs($this->admin)
             ->get(route('admin.users.index'))
             ->assertStatus(200);
    }

    /** @test */
    public function user_list_is_displayed_correctly()
    {
        $this->actingAs($this->admin)
             ->get(route('admin.users.index'))
             ->assertSee($this->user->name)
             ->assertSee($this->user->email);
    }

    /** @test */
    public function search_functionality_works()
    {
        $userToFind = User::factory()->create(['name' => 'John Doe Search']);

        $this->actingAs($this->admin)
             ->get(route('admin.users.index', ['search' => 'John Doe Search']))
             ->assertSee($userToFind->name)
             ->assertDontSee($this->user->name);
    }

    /** @test */
    public function admin_can_ban_a_user()
    {
        $this->actingAs($this->admin)
             ->post(route('admin.users.ban', $this->user));

        $this->assertNotNull($this->user->fresh()->banned_at);
    }

    /** @test */
    public function admin_can_unban_a_user()
    {
        $this->user->update(['banned_at' => now()]);
        $this->assertNotNull($this->user->fresh()->banned_at);

        $this->actingAs($this->admin)
             ->post(route('admin.users.unban', $this->user));

        $this->assertNull($this->user->fresh()->banned_at);
    }

    /** @test */
    public function a_banned_user_cannot_log_in()
    {
        // Bannir l'utilisateur
        $this->user->update(['banned_at' => now()]);

        // Tenter de se connecter
        $response = $this->post('/login', [
            'email' => $this->user->email,
            'password' => 'password',
        ]);

        $response->assertSessionHasErrors();
        $this->assertGuest();
    }

    /** @test */
    public function a_logged_in_user_is_logged_out_on_next_request_if_banned()
    {
        // Se connecter en tant qu'utilisateur normal
        $this->actingAs($this->user);

        // L'administrateur bannit l'utilisateur
        $this->actingAs($this->admin)->post(route('admin.users.ban', $this->user));

        // On rafraîchit l'instance de l'utilisateur pour récupérer le statut de banni de la BDD
        $this->user->refresh();

        // L'utilisateur tente d'accéder à une page protégée
        $this->actingAs($this->user)
             ->get('/dashboard')
             ->assertRedirect('/login');

        $this->assertGuest();
    }
}
