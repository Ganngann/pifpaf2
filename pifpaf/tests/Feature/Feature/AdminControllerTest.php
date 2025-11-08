<?php

namespace Tests\Feature\Feature;

use App\Models\Dispute;
use App\Models\Item;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AdminControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function admin_can_view_dashboard_with_stats(): void
    {
        // Créer un utilisateur administrateur
        $admin = User::factory()->create(['role' => 'admin']);

        // Créer des données de test
        User::factory()->count(5)->create();
        Item::factory()->count(10)->create();
        Transaction::factory()->count(3)->create();
        Dispute::factory()->count(2)->create(['status' => 'open']);
        Dispute::factory()->create(['status' => 'closed']);

        // Agir en tant qu'administrateur
        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        // Assertions
        $response->assertOk();
        $response->assertViewIs('admin.dashboard');
        $response->assertViewHas('userCount', User::count());
        $response->assertViewHas('itemCount', Item::count());
        $response->assertViewHas('transactionCount', Transaction::count());
        $response->assertViewHas('openDisputesCount', 2);
    }

    #[Test]
    public function non_admin_user_is_redirected_from_admin_dashboard(): void
    {
        // Créer un utilisateur non-administrateur
        $user = User::factory()->create(['role' => 'user']);

        // Agir en tant qu'utilisateur non-administrateur
        $response = $this->actingAs($user)->get(route('admin.dashboard'));

        // Assertion
        $response->assertStatus(403);
    }

    #[Test]
    public function guest_is_redirected_from_admin_dashboard(): void
    {
        // Accéder à la route en tant qu'invité
        $response = $this->get(route('admin.dashboard'));

        // Assertion
        $response->assertRedirect(route('login'));
    }
}
