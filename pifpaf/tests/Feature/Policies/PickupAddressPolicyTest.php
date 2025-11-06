<?php

namespace Tests\Feature\Policies;

use App\Models\PickupAddress;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PickupAddressPolicyTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_can_view_pickup_address()
    {
        $user = User::factory()->create();
        $pickupAddress = PickupAddress::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user);

        $this->assertTrue($user->can('view', $pickupAddress));
    }

    public function test_non_owner_cannot_view_pickup_address()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $pickupAddress = PickupAddress::factory()->create(['user_id' => $otherUser->id]);

        $this->actingAs($user);

        $this->assertFalse($user->can('view', $pickupAddress));
    }

    public function test_owner_can_update_pickup_address()
    {
        $user = User::factory()->create();
        $pickupAddress = PickupAddress::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user);

        $this->assertTrue($user->can('update', $pickupAddress));
    }

    public function test_non_owner_cannot_update_pickup_address()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $pickupAddress = PickupAddress::factory()->create(['user_id' => $otherUser->id]);

        $this->actingAs($user);

        $this->assertFalse($user->can('update', $pickupAddress));
    }

    public function test_owner_can_delete_pickup_address()
    {
        $user = User::factory()->create();
        $pickupAddress = PickupAddress::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user);

        $this->assertTrue($user->can('delete', $pickupAddress));
    }

    public function test_non_owner_cannot_delete_pickup_address()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $pickupAddress = PickupAddress::factory()->create(['user_id' => $otherUser->id]);

        $this->actingAs($user);

        $this->assertFalse($user->can('delete', $pickupAddress));
    }
}
