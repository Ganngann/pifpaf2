<?php

namespace Tests\Feature;

use App\Models\Address;
use App\Models\User;
use App\Models\Item;
use App\Models\Offer;
use App\Models\Transaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AdminDashboardTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function guests_are_redirected_from_admin_dashboard()
    {
        $this->get(route('admin.dashboard'))
            ->assertRedirect(route('login'));
    }

    #[Test]
    public function non_admin_users_are_forbidden()
    {
        $user = User::factory()->create(['role' => 'user']);

        $this->actingAs($user)
            ->get(route('admin.dashboard'))
            ->assertForbidden();
    }

    #[Test]
    public function admin_can_see_dashboard_with_stats()
    {
        // Arrange
        // Crée 1 admin + 5 utilisateurs standards, pour un total de 6.
        $admin = User::factory()->create(['role' => 'admin']);
        $users = User::factory()->count(5)->create();
        $this->assertEquals(6, User::count());

        // Crée une adresse de ramassage pour le premier utilisateur (le vendeur).
        $address = Address::factory()->create(['user_id' => $users->first()->id]);

        // Crée 10 annonces "simples" pour le premier utilisateur.
        Item::factory()->count(10)->create([
            'user_id' => $users->first()->id,
            'address_id' => $address->id,
        ]);
        $this->assertEquals(10, Item::count());
        $this->assertEquals(6, User::count(), "Le nombre d'utilisateurs ne devrait pas changer après la création d'items simples.");

        // Crée 3 annonces plus complexes avec des offres et transactions.
        Item::factory()->count(3)
            ->for($users->get(0), 'user') // Le vendeur
            ->state(['address_id' => $address->id]) // Réutilise l'adresse
            ->has(
                Offer::factory()
                    ->for($users->get(1), 'user') // L'acheteur
                    ->has(Transaction::factory(), 'transaction')
            , 'offers')
            ->create();

        $this->assertEquals(13, Item::count());
        $this->assertEquals(3, Transaction::count());
        $this->assertEquals(6, User::count()); // Vérification cruciale

        // Act
        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        // Assert
        $response->assertStatus(200);
        $response->assertViewIs('admin.dashboard');
        $response->assertViewHas('userCount', 6);
        $response->assertViewHas('itemCount', 13);
        $response->assertViewHas('transactionCount', 3);

        $response->assertSee('Statistiques Clés');
        $response->assertSee('>6</p>', false);
        $response->assertSee('>13</p>', false);
        $response->assertSee('>3</p>', false);
    }
}
