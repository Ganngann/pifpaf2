<?php

namespace Tests\Feature\Policies;

use App\Models\ShippingAddress;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShippingAddressPolicyTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_can_view_shipping_address()
    {
        $user = User::factory()->create();
        $shippingAddress = ShippingAddress::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user);

        $this->assertTrue($user->can('view', $shippingAddress));
    }

    public function test_non_owner_cannot_view_shipping_address()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $shippingAddress = ShippingAddress::factory()->create(['user_id' => $otherUser->id]);

        $this->actingAs($user);

        $this->assertFalse($user->can('view', $shippingAddress));
    }

    public function test_owner_can_update_shipping_address()
    {
        $user = User::factory()->create();
        $shippingAddress = ShippingAddress::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user);

        $this->assertTrue($user->can('update', $shippingAddress));
    }

    public function test_non_owner_cannot_update_shipping_address()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $shippingAddress = ShippingAddress::factory()->create(['user_id' => $otherUser->id]);

        $this->actingAs($user);

        $this->assertFalse($user->can('update', $shippingAddress));
    }

    public function test_owner_can_delete_shipping_address()
    {
        $user = User::factory()->create();
        $shippingAddress = ShippingAddress::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user);

        $this->assertTrue($user->can('delete', $shippingAddress));
    }

    public function test_non_owner_cannot_delete_shipping_address()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $shippingAddress = ShippingAddress::factory()->create(['user_id' => $otherUser->id]);

        $this->actingAs($user);

        $this->assertFalse($user->can('delete', $shippingAddress));
    }
}
