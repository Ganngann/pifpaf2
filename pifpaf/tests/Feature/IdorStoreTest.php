<?php

namespace Tests\Feature;

use App\Models\AiRequest;
use App\Models\User;
use App\Models\Address;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class IdorStoreTest extends TestCase
{
    use RefreshDatabase;

    public function test_idor_in_store()
    {
        Storage::fake('public');

        $victim = User::factory()->create();
        $attacker = User::factory()->create();
        $address = Address::factory()->create(['user_id' => $attacker->id, 'is_for_pickup' => true]);

        $file = UploadedFile::fake()->image('victim_image.jpg', 100, 100);
        $imagePath = $file->storeAs('ai_images', 'victim_image.jpg', 'public');

        $aiRequest = AiRequest::create([
            'user_id' => $victim->id,
            'image_path' => $imagePath,
            'status' => 'completed',
        ]);

        $response = $this->actingAs($attacker)->post(route('items.store'), [
            'title' => 'Stolen Item',
            'description' => 'Test',
            'category' => 'Autre',
            'price' => 10,
            'image_path' => $imagePath,
            'pickup_available' => true,
            'address_id' => $address->id,
        ]);

        // Since it's a redirect after successful store, we check if the file was moved.
        // If it was moved, the vulnerability exists.
        if (Storage::disk('public')->exists('item_images/1/victim_image.jpg') && !Storage::disk('public')->exists($imagePath)) {
            $this->fail('Vulnerability exists: Image was stolen and moved to attacker\'s item.');
        }

        $this->assertTrue(Storage::disk('public')->exists($imagePath), 'Original image should still exist.');
    }
}
