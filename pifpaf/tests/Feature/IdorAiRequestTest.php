<?php

namespace Tests\Feature;

use App\Models\AiRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class IdorAiRequestTest extends TestCase
{
    use RefreshDatabase;

    public function test_idor_in_crop_preview()
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

        $response = $this->actingAs($attacker)->getJson(route('ai.requests.crop_preview', [
            'image_path' => $imagePath,
            'box' => json_encode(['x1' => 0, 'y1' => 0, 'x2' => 500, 'y2' => 500])
        ]));

        if ($response->status() === 200) {
            $this->fail('Vulnerability exists: Request to cropPreview was successful when it should have been 403 Forbidden.');
        }

        $response->assertStatus(403);
    }
}
