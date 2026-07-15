<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Item;
use App\Models\Address;
use App\Models\Offer;
use App\Enums\ItemStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ItemAddressIdorTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function user_cannot_use_another_users_address_for_pickup_in_store(): void
    {
        Storage::fake('public');
        $attacker = User::factory()->create();
        $victim = User::factory()->create();
        $victimAddress = Address::factory()->create(['user_id' => $victim->id]);

        $response = $this->actingAs($attacker)->post(route('items.store'), [
            'title' => 'Test Item',
            'description' => 'Test Description',
            'category' => 'Autre',
            'price' => 10,
            'pickup_available' => true,
            'address_id' => $victimAddress->id,
            'images' => [UploadedFile::fake()->image('image.jpg')],
        ]);

        $response->assertSessionHasErrors(['address_id']);
        $this->assertDatabaseMissing('items', [
            'title' => 'Test Item',
            'address_id' => $victimAddress->id,
        ]);
    }

    #[Test]
    public function user_cannot_use_another_users_address_for_pickup_in_update(): void
    {
        $attacker = User::factory()->create();
        $item = Item::factory()->create(['user_id' => $attacker->id]);
        $victim = User::factory()->create();
        $victimAddress = Address::factory()->create(['user_id' => $victim->id]);

        $response = $this->actingAs($attacker)->put(route('items.update', $item), [
            'title' => 'Test Item',
            'description' => 'Test Description',
            'category' => 'Autre',
            'price' => 10,
            'pickup_available' => true,
            'address_id' => $victimAddress->id,
        ]);

        $response->assertSessionHasErrors(['address_id']);
        $this->assertDatabaseMissing('items', [
            'id' => $item->id,
            'address_id' => $victimAddress->id,
        ]);
    }

    #[Test]
    public function user_cannot_use_another_users_address_for_payment_delivery(): void
    {
        $attacker = User::factory()->create();
        $victim = User::factory()->create();
        $victimAddress = Address::factory()->create(['user_id' => $victim->id]);

        $item = Item::factory()->create(['user_id' => $victim->id]);
        $offer = Offer::factory()->create([
            'item_id' => $item->id,
            'user_id' => $attacker->id,
            'status' => 'accepted',
            'delivery_method' => 'delivery'
        ]);

        $response = $this->actingAs($attacker)->get(route('payment.create', [
            'offer' => $offer->id,
            'address_id' => $victimAddress->id
        ]));

        $response->assertSessionHasErrors(['address_id']);
    }
}
