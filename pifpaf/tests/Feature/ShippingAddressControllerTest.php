<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\ShippingAddress;

class ShippingAddressControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_create_shipping_address()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('profile.shipping_addresses.store'), [
            'name' => 'Domicile',
            'street' => '123 Rue de la République',
            'city' => 'Paris',
            'postal_code' => '75001',
            'country' => 'France',
        ]);

        $response->assertRedirect(route('profile.addresses.index'));
        $this->assertDatabaseHas('shipping_addresses', [
            'user_id' => $user->id,
            'name' => 'Domicile',
        ]);
    }

    public function test_authenticated_user_can_update_their_shipping_address()
    {
        $user = User::factory()->create();
        $address = ShippingAddress::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->put(route('profile.shipping_addresses.update', $address), [
            'name' => 'Domicile Mis à Jour',
            'street' => '456 Avenue des Champs-Élysées',
            'city' => 'Paris',
            'postal_code' => '75008',
            'country' => 'France',
        ]);

        $response->assertRedirect(route('profile.addresses.index'));
        $this->assertDatabaseHas('shipping_addresses', [
            'id' => $address->id,
            'name' => 'Domicile Mis à Jour',
        ]);
    }

    public function test_authenticated_user_can_delete_their_shipping_address()
    {
        $user = User::factory()->create();
        $address = ShippingAddress::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->delete(route('profile.shipping_addresses.destroy', $address));

        $response->assertRedirect(route('profile.addresses.index'));
        $this->assertDatabaseMissing('shipping_addresses', [
            'id' => $address->id,
        ]);
    }

    public function test_user_cannot_access_edit_page_of_another_user()
    {
        $user = User::factory()->create();
        $anotherUser = User::factory()->create();
        $address = ShippingAddress::factory()->create(['user_id' => $anotherUser->id]);

        $response = $this->actingAs($user)->get(route('profile.shipping_addresses.edit', $address));

        $response->assertStatus(403);
    }
}
