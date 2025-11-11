<?php

namespace Tests\Feature\Feature;

use App\Models\Address;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class AddressControllerTest extends TestCase
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
        $address = Address::factory()->create(['user_id' => $this->user->id]);

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
        $pickupAddress = Address::factory()->create(['user_id' => $this->user->id, 'name' => 'My Pickup Address']);
        $shippingAddress = Address::factory()->delivery()->create(['user_id' => $this->user->id, 'name' => 'My Shipping Address']);
        $otherAddress = Address::factory()->create(['user_id' => $this->otherUser->id, 'name' => 'Other User Address']);

        $this->actingAs($this->user)
            ->get(route('profile.addresses.index'))
            ->assertStatus(200)
            ->assertSee('My Pickup Address')
            ->assertSee('My Shipping Address')
            ->assertDontSee('Other User Address');
    }

    #[Test]
    public function user_can_store_a_new_address_for_both_pickup_and_delivery(): void
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
            'country' => 'BE',
            'is_for_pickup' => true,
            'is_for_delivery' => true,
        ];

        $this->actingAs($this->user)
            ->post(route('profile.addresses.store'), $addressData)
            ->assertRedirect(route('profile.addresses.index'));

        $this->assertDatabaseHas('addresses', [
            'user_id' => $this->user->id,
            'name' => 'Maison',
            'is_for_pickup' => true,
            'is_for_delivery' => true,
            'latitude' => '50.8503',
            'longitude' => '4.3517',
        ]);
    }

    #[Test]
    public function user_must_select_at_least_one_address_type(): void
    {
        $addressData = [
            'name' => 'Adresse Invalide',
            'street' => 'Rue de l\'Erreur 1',
            'city' => 'Testville',
            'postal_code' => '1234',
            'country' => 'FR', // Valid country
            'is_for_pickup' => false,
            'is_for_delivery' => false,
        ];

        $this->actingAs($this->user)
            ->post(route('profile.addresses.store'), $addressData)
            ->assertSessionHasErrors('type');
    }


    #[Test]
    public function user_can_update_their_own_address(): void
    {
        $address = Address::factory()->create(['user_id' => $this->user->id, 'is_for_pickup' => true, 'is_for_delivery' => false]);

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
            'country' => 'BE',
            'is_for_pickup' => true,
            'is_for_delivery' => true,
        ];

        $this->actingAs($this->user)
            ->put(route('profile.addresses.update', $address), $updatedData)
            ->assertRedirect(route('profile.addresses.index'));

        $this->assertDatabaseHas('addresses', [
            'id' => $address->id,
            'name' => 'Bureau',
            'city' => 'Anvers',
            'is_for_delivery' => true,
            'latitude' => '51.2194',
        ]);
    }

    #[Test]
    public function user_can_delete_their_own_address(): void
    {
        $address = Address::factory()->create(['user_id' => $this->user->id]);

        $this->actingAs($this->user)
            ->delete(route('profile.addresses.destroy', $address))
            ->assertRedirect(route('profile.addresses.index'));

        $this->assertDatabaseMissing('addresses', ['id' => $address->id]);
    }

    #[Test]
    public function user_cannot_delete_another_users_address(): void
    {
        $address = Address::factory()->create(['user_id' => $this->otherUser->id]);

        $this->actingAs($this->user)
            ->delete(route('profile.addresses.destroy', $address))
            ->assertStatus(403);
    }
}
