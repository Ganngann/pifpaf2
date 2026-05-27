<?php

namespace Tests\Feature;

use App\Models\AiRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class IdorTest extends TestCase
{
    use RefreshDatabase;

    public function test_idor_in_create_from_ai()
    {
        Storage::fake('public');

        $victim = User::factory()->create();
        $attacker = User::factory()->create();

        // Use a real image to prevent Intervention\Image from throwing an exception
        $file = UploadedFile::fake()->image('victim_image.jpg', 100, 100);
        $imagePath = $file->storeAs('ai_images', 'victim_image.jpg', 'public');

        $aiRequest = AiRequest::create([
            'user_id' => $victim->id,
            'image_path' => $imagePath,
            'status' => 'completed',
        ]);

        $response = $this->actingAs($attacker)->postJson(route('items.create-from-ai'), [
            'original_image_path' => $imagePath,
            'item_data' => json_encode([
                'title' => 'Stolen Item',
                'description' => 'Test',
                'category' => 'Autre',
                'price' => 10,
                'box' => ['x1' => 0, 'y1' => 0, 'x2' => 500, 'y2' => 500] // Values / 1000 = 0.5 * 100 = 50px
            ]),
            'item_index' => 0,
        ]);

        // If the vulnerability exists, the request will be processed successfully (returns 200)
        // If it's fixed, we expect 403 Forbidden because $attacker doesn't own $aiRequest
        if ($response->status() === 200) {
            $this->fail('Vulnerability exists: Request was successful when it should have been 403 Forbidden.');
        }

        $response->assertStatus(403);
    }
}
