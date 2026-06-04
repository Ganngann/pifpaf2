<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\AiRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ItemControllerCreateFromAiTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_from_ai_prevents_idor()
    {
        Storage::fake('public');

        $image = UploadedFile::fake()->image('test.jpg');
        $imagePath = $image->store('ai_images', 'public');

        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $aiRequest = AiRequest::create([
            'user_id' => $user1->id,
            'image_path' => $imagePath,
            'status' => 'completed'
        ]);

        $this->actingAs($user2);

        $response = $this->postJson(route('items.create-from-ai'), [
            'original_image_path' => $imagePath,
            'item_index' => 0,
            'item_data' => json_encode([
                'title' => 'Test',
                'description' => 'Test',
                'price' => 10,
                'category' => 'Autre',
                'box' => ['x1' => 0, 'y1' => 0, 'x2' => 100, 'y2' => 100]
            ])
        ]);

        $response->assertStatus(403);
    }
}
