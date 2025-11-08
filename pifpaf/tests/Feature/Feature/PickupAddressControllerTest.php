<?php

namespace Tests\Feature\Feature;

use App\Models\User;
use App\Models\PickupAddress;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class PickupAddressControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private User $otherUser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->otherUser = User::factory()->create();
    }

    #[Test]
    public function guests_cannot_access_any_address_routes(): void
    {
        $address = PickupAddress::factory()->create(['user_id' => $this->user->id]);

        $this->get(route('profile.addresses.index'))->assertRedirect('login');
        $this->get(route('profile.addresses.create'))->assertRedirect('login');
        $this->post(route('profile.addresses.store'))->assertRedirect('login');
        $this->get(route('profile.addresses.edit', $address))->assertRedirect('login');
        $this->put(route('profile.addresses.update', $address))->assertRedirect('login');
        $this->delete(route('profile.addresses.destroy', $address))->assertRedirect('login');
    }

    #[Test]
    public function user_can_see_their_own_addresses_on_index_page(): void
    {
        $myAddress = PickupAddress::factory()->create(['user_id' => $this->user->id, 'name' => 'My Address']);
        $otherAddress = PickupAddress::factory()->create(['user_id' => $this->otherUser->id, 'name' => 'Other User Address']);

        $this->actingAs($this->user)
            ->get(route('profile.addresses.index'))
            ->assertStatus(200)
            ->assertSee('My Address')
            ->assertDontSee('Other User Address');
    }

    #[Test]
    public function user_can_see_the_create_address_form(): void
    {
        $this->actingAs($this->user)
            ->get(route('profile.addresses.create'))
            ->assertStatus(200)
            ->assertSee('Ajouter une nouvelle adresse');
    }

    #[Test]
    public function user_can_store_a_new_address(): void
    {
        Http::fake([
            'geocode.maps.co/*' => Http::response([
                ['lat' => '50.8503', 'lon' => '4.3517']
            ], 200)
        ]);

        $addressData = [
            'name' => 'Maison',
            'street' => 'Grand Place 1',
            'city' => 'Bruxelles',
            'postal_code' => '1000',
        ];

        $this->actingAs($this->user)
            ->post(route('profile.addresses.store'), $addressData)
            ->assertRedirect(route('profile.addresses.index'))
            ->assertSessionHas('success', 'Adresse ajoutée avec succès.');

        $this->assertDatabaseHas('pickup_addresses', [
            'user_id' => $this->user->id,
            'name' => 'Maison',
            'latitude' => '50.8503',
            'longitude' => '4.3517',
        ]);
    }

    #[Test]
    public function store_fails_with_invalid_data(): void
    {
        $this->actingAs($this->user)
            ->post(route('profile.addresses.store'), ['name' => ''])
            ->assertSessionHasErrors(['name', 'street', 'city', 'postal_code']);
    }

    #[Test]
    public function user_can_see_edit_form_for_their_own_address(): void
    {
        $address = PickupAddress::factory()->create(['user_id' => $this->user->id]);

        $this->actingAs($this->user)
            ->get(route('profile.addresses.edit', $address))
            ->assertStatus(200)
            ->assertSee('Modifier l\'adresse');
    }

    #[Test]
    public function user_cannot_see_edit_form_for_another_users_address(): void
    {
        $address = PickupAddress::factory()->create(['user_id' => $this->otherUser->id]);

        $this->actingAs($this->user)
            ->get(route('profile.addresses.edit', $address))
            ->assertStatus(403);
    }

    #[Test]
    public function user_can_update_their_own_address(): void
    {
        $address = PickupAddress::factory()->create(['user_id' => $this->user->id]);

        Http::fake([
            'geocode.maps.co/*' => Http::response([
                ['lat' => '51.2194', 'lon' => '4.4025']
            ], 200)
        ]);

        $updatedData = [
            'name' => 'Bureau',
            'street' => 'Meir 1',
            'city' => 'Anvers',
            'postal_code' => '2000',
        ];

        $this->actingAs($this->user)
            ->put(route('profile.addresses.update', $address), $updatedData)
            ->assertRedirect(route('profile.addresses.index'))
            ->assertSessionHas('success', 'Adresse mise à jour avec succès.');

        $this->assertDatabaseHas('pickup_addresses', [
            'id' => $address->id,
            'name' => 'Bureau',
            'city' => 'Anvers',
            'latitude' => '51.2194',
        ]);
    }

    #[Test]
    public function user_cannot_update_another_users_address(): void
    {
        $address = PickupAddress::factory()->create(['user_id' => $this->otherUser->id]);

        $this->actingAs($this->user)
            ->put(route('profile.addresses.update', $address), [])
            ->assertStatus(403);
    }

    #[Test]
    public function user_can_delete_their_own_address(): void
    {
        $address = PickupAddress::factory()->create(['user_id' => $this->user->id]);

        $this->actingAs($this->user)
            ->delete(route('profile.addresses.destroy', $address))
            ->assertRedirect(route('profile.addresses.index'))
            ->assertSessionHas('success', 'Adresse supprimée avec succès.');

        $this->assertDatabaseMissing('pickup_addresses', ['id' => $address->id]);
    }

    #[Test]
    public function user_cannot_delete_another_users_address(): void
    {
        $address = PickupAddress::factory()->create(['user_id' => $this->otherUser->id]);

        $this->actingAs($this->user)
            ->delete(route('profile.addresses.destroy', $address))
            ->assertStatus(403);
    }
}
