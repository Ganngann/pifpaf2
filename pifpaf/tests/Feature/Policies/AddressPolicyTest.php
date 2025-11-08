<?php

namespace Tests\Feature\Policies;

use App\Models\Address;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AddressPolicyTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_can_view_address()
    {
        $user = User::factory()->create();
        $address = Address::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user);

        $this->assertTrue($user->can('view', $address));
    }

    public function test_non_owner_cannot_view_address()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $address = Address::factory()->create(['user_id' => $otherUser->id]);

        $this->actingAs($user);

        $this->assertFalse($user->can('view', $address));
    }

    public function test_owner_can_update_address()
    {
        $user = User::factory()->create();
        $address = Address::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user);

        $this->assertTrue($user->can('update', $address));
    }

    public function test_non_owner_cannot_update_address()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $address = Address::factory()->create(['user_id' => $otherUser->id]);

        $this->actingAs($user);

        $this->assertFalse($user->can('update', $address));
    }

    public function test_owner_can_delete_address()
    {
        $user = User::factory()->create();
        $address = Address::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user);

        $this->assertTrue($user->can('delete', $address));
    }

    public function test_non_owner_cannot_delete_address()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $address = Address::factory()->create(['user_id' => $otherUser->id]);

        $this->actingAs($user);

        $this->assertFalse($user->can('delete', $address));
    }
}
