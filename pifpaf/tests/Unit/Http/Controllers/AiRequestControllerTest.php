<?php

namespace Tests\Unit\Http\Controllers;

use App\Jobs\ProcessAiImage;
use App\Models\AiRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AiRequestControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function store_creates_ai_request_and_dispatches_job()
    {
        Storage::fake('public');
        Queue::fake();

        $user = User::factory()->create();
        $this->actingAs($user);

        $file = UploadedFile::fake()->image('test.jpg');

        $response = $this->post(route('ai-requests.store'), [
            'image' => $file,
        ]);

        $response->assertRedirect(route('ai-requests.index'));
        $this->assertDatabaseHas('ai_requests', [
            'user_id' => $user->id,
            'status' => 'pending',
        ]);
        Queue::assertPushed(ProcessAiImage::class);
    }

    #[Test]
    public function index_displays_user_requests()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        AiRequest::factory()->count(3)->create(['user_id' => $user->id]);

        $response = $this->get(route('ai-requests.index'));

        $response->assertStatus(200);
        $response->assertViewHas('requests', function ($requests) {
            return $requests->count() === 3;
        });
    }
}
