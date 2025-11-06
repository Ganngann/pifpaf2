<?php

namespace Tests\Feature\Http\Controllers;

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

    public function test_index_displays_user_ai_requests()
    {
        $user = User::factory()->create();
        AiRequest::factory()->count(3)->create(['user_id' => $user->id]);

        $this->actingAs($user);
        $response = $this->get(route('ai-requests.index'));

        $response->assertStatus(200);
        $response->assertViewIs('ai-requests.index');
        $response->assertViewHas('requests', function ($requests) {
            return $requests->count() === 3;
        });
    }

    public function test_store_creates_ai_request_and_dispatches_job()
    {
        Queue::fake();
        Storage::fake('public');

        $user = User::factory()->create();
        $this->actingAs($user);

        $file = UploadedFile::fake()->image('test_image.jpg');

        $response = $this->post(route('ai-requests.store'), [
            'image' => $file,
        ]);

        $response->assertRedirect(route('ai-requests.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('ai_requests', [
            'user_id' => $user->id,
            'status' => 'pending',
        ]);

        $aiRequest = AiRequest::first();
        Storage::disk('public')->assertExists($aiRequest->image_path);
        Queue::assertPushed(ProcessAiImage::class, function ($job) use ($aiRequest) {
            return $job->aiRequest->id === $aiRequest->id;
        });
    }

    public function test_store_validates_image_file()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->post(route('ai-requests.store'), [
            'image' => 'not_an_image',
        ]);

        $response->assertSessionHasErrors('image');
    }

    public function test_retry_dispatches_job_for_failed_request()
    {
        Queue::fake();
        $user = User::factory()->create();
        $aiRequest = AiRequest::factory()->create([
            'user_id' => $user->id,
            'status' => 'failed',
            'retry_count' => 0,
        ]);

        $this->actingAs($user);
        $response = $this->post(route('ai-requests.retry', $aiRequest));

        $response->assertRedirect(route('ai-requests.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('ai_requests', [
            'id' => $aiRequest->id,
            'status' => 'pending',
            'retry_count' => 1,
        ]);

        Queue::assertPushed(ProcessAiImage::class, function ($job) use ($aiRequest) {
            return $job->aiRequest->id === $aiRequest->id;
        });
    }

    public function test_retry_is_forbidden_for_other_users_request()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $aiRequest = AiRequest::factory()->create(['user_id' => $otherUser->id, 'status' => 'failed']);

        $this->actingAs($user);
        $response = $this->post(route('ai-requests.retry', $aiRequest));

        $response->assertStatus(403);
    }

    public function test_retry_is_not_allowed_for_non_failed_requests()
    {
        $user = User::factory()->create();
        $aiRequest = AiRequest::factory()->create(['user_id' => $user->id, 'status' => 'completed']);

        $this->actingAs($user);
        $response = $this->post(route('ai-requests.retry', $aiRequest));

        $response->assertSessionHas('error');
    }

    public function test_retry_is_limited_to_max_attempts()
    {
        $user = User::factory()->create();
        $aiRequest = AiRequest::factory()->create([
            'user_id' => $user->id,
            'status' => 'failed',
            'retry_count' => 3,
        ]);

        $this->actingAs($user);
        $response = $this->post(route('ai-requests.retry', $aiRequest));

        $response->assertSessionHas('error');
    }
}
