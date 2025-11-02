<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\PickupAddress;

class PickupAddressAuthorizationTest extends TestCase
{
    use RefreshDatabase;


    public function test_user_cannot_access_edit_page_of_another_user()
    {
        $user = User::factory()->create();
        $anotherUser = User::factory()->create();
        $address = PickupAddress::factory()->create(['user_id' => $anotherUser->id]);

        $response = $this->actingAs($user)->get(route('profile.addresses.edit', $address));

        $response->assertStatus(403);
    }
}
