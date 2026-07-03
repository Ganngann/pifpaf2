<?php

namespace Tests\Feature;

use App\Models\Address;
use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ItemIdorTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function user_cannot_assign_others_address_to_their_item()
    {
        Storage::fake('public');

        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $addressUser2 = Address::factory()->create([
            'user_id' => $user2->id,
            'is_for_pickup' => true,
        ]);

        $response = $this->actingAs($user1)->post(route('items.store'), [
            'title' => 'Test Item',
            'description' => 'Test Description',
            'category' => 'Autre',
            'price' => 10,
            'pickup_available' => true,
            'address_id' => $addressUser2->id,
            'images' => [UploadedFile::fake()->image('image.jpg')],
        ]);

        $response->assertSessionHasErrors('address_id');
        $this->assertDatabaseMissing('items', [
            'user_id' => $user1->id,
            'address_id' => $addressUser2->id,
        ]);
    }

    #[Test]
    public function user_cannot_assign_others_address_when_updating_item()
    {
        Storage::fake('public');

        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $item = Item::factory()->create([
            'user_id' => $user1->id,
        ]);

        $addressUser2 = Address::factory()->create([
            'user_id' => $user2->id,
            'is_for_pickup' => true,
        ]);

        $response = $this->actingAs($user1)->put(route('items.update', $item), [
            'title' => 'Updated Item',
            'description' => 'Updated Description',
            'category' => 'Autre',
            'price' => 20,
            'pickup_available' => true,
            'address_id' => $addressUser2->id,
        ]);

        $response->assertSessionHasErrors('address_id');
        $this->assertDatabaseMissing('items', [
            'id' => $item->id,
            'address_id' => $addressUser2->id,
        ]);
    }
}
