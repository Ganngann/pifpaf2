<?php

namespace Tests\Feature;

use App\Models\Address;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ItemDeliveryOptionsTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_item_with_delivery_options(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $address = Address::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->post(route('items.store'), [
            'title' => 'Test Item',
            'description' => 'Test Description',
            'category' => 'Sport',
            'price' => 100,
            'images' => [UploadedFile::fake()->image('item.jpg')],
            'delivery_available' => true,
            'pickup_available' => true,
            'address_id' => $address->id,
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertDatabaseHas('items', [
            'title' => 'Test Item',
            'delivery_available' => true,
            'pickup_available' => true,
            'address_id' => $address->id,
        ]);
    }

    public function test_can_update_item_with_delivery_options(): void
    {
        $user = User::factory()->create();
        $item = Item::factory()->create(['user_id' => $user->id]);
        $address = Address::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->put(route('items.update', $item), [
            'title' => 'Updated Title',
            'description' => $item->description,
            'category' => $item->category,
            'price' => $item->price,
            'delivery_available' => false,
            'pickup_available' => true,
            'address_id' => $address->id,
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertDatabaseHas('items', [
            'id' => $item->id,
            'title' => 'Updated Title',
            'delivery_available' => false,
            'pickup_available' => true,
            'address_id' => $address->id,
        ]);
    }

    public function test_pickup_address_is_required_when_pickup_is_available(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('items.store'), [
            'title' => 'Test Item',
            'description' => 'Test Description',
            'category' => 'Sport',
            'price' => 100,
            'images' => [UploadedFile::fake()->image('item.jpg')],
            'pickup_available' => true,
            'address_id' => null,
        ]);

        $response->assertSessionHasErrors('address_id');
    }

    public function test_item_is_created_without_pickup_address_if_pickup_is_unavailable(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('items.store'), [
            'title' => 'Another Test Item',
            'description' => 'Test Description',
            'category' => 'Maison',
            'price' => 50,
            'images' => [UploadedFile::fake()->image('item2.jpg')],
            'delivery_available' => true,
            'pickup_available' => false,
            'address_id' => null, // Should be ignored
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertDatabaseHas('items', [
            'title' => 'Another Test Item',
            'delivery_available' => true,
            'pickup_available' => false,
            'address_id' => null,
        ]);
    }
}
