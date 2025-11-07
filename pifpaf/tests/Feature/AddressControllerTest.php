<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Address;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AddressControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_pickup_address()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $addressData = [
            'type' => 'pickup',
            'name' => 'Home',
            'street' => '123 Main St',
            'city' => 'Anytown',
            'postal_code' => '12345',
        ];

        $response = $this->post(route('profile.addresses.store'), $addressData);

        $response->assertRedirect(route('profile.addresses.index'));
        $this->assertDatabaseHas('addresses', $addressData);
    }

    public function test_user_can_create_shipping_address()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $addressData = [
            'type' => 'shipping',
            'name' => 'Work',
            'street' => '456 Oak Ave',
            'city' => 'Someville',
            'postal_code' => '67890',
            'country' => 'Belgium',
        ];

        $response = $this->post(route('profile.addresses.store'), $addressData);

        $response->assertRedirect(route('profile.addresses.index'));
        $this->assertDatabaseHas('addresses', $addressData);
    }

    public function test_user_can_update_their_address()
    {
        $user = User::factory()->create();
        $address = Address::factory()->create(['user_id' => $user->id, 'type' => 'pickup']);
        $this->actingAs($user);

        $updatedData = [
            'name' => 'Updated Name',
            'street' => '987 Updated St',
            'city' => 'Newville',
            'postal_code' => '54321',
        ];

        $response = $this->put(route('profile.addresses.update', $address), $updatedData);

        $response->assertRedirect(route('profile.addresses.index'));
        $this->assertDatabaseHas('addresses', array_merge(['id' => $address->id], $updatedData));
    }

    public function test_user_cannot_update_other_users_address()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $address = Address::factory()->create(['user_id' => $user2->id]);
        $this->actingAs($user1);

        $response = $this->put(route('profile.addresses.update', $address), ['name' => 'New Name']);

        $response->assertStatus(403);
    }

    public function test_user_can_delete_their_address()
    {
        $user = User::factory()->create();
        $address = Address::factory()->create(['user_id' => $user->id]);
        $this->actingAs($user);

        $response = $this->delete(route('profile.addresses.destroy', $address));

        $response->assertRedirect(route('profile.addresses.index'));
        $this->assertDatabaseMissing('addresses', ['id' => $address->id]);
    }
}
