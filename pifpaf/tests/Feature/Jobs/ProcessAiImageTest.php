<?php

namespace Tests\Feature\Jobs;

use App\Jobs\ProcessAiImage;
use App\Models\AiRequest;
use App\Models\User;
use App\Services\GoogleAiService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Mockery\MockInterface;
use Tests\TestCase;

class ProcessAiImageTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    public function test_job_updates_status_to_completed_on_success()
    {
        $user = User::factory()->create();
        $aiRequest = AiRequest::factory()->create(['user_id' => $user->id, 'status' => 'pending']);
        $fakeResponse = ['success' => true, 'data' => ['some' => 'data']];

        $this->mock(GoogleAiService::class, function (MockInterface $mock) use ($fakeResponse) {
            $mock->shouldReceive('analyzeImage')->once()->andReturn($fakeResponse);
        });

        ProcessAiImage::dispatchSync($aiRequest);

        $this->assertDatabaseHas('ai_requests', [
            'id' => $aiRequest->id,
            'status' => 'completed',
            'result' => json_encode(['some' => 'data']),
        ]);
    }

    public function test_job_updates_status_to_failed_on_api_error()
    {
        $user = User::factory()->create();
        $aiRequest = AiRequest::factory()->create(['user_id' => $user->id, 'status' => 'pending']);
        $fakeResponse = ['success' => false, 'error' => 'API Error', 'raw_response' => 'Raw error details'];

        $this->mock(GoogleAiService::class, function (MockInterface $mock) use ($fakeResponse) {
            $mock->shouldReceive('analyzeImage')->once()->andReturn($fakeResponse);
        });

        ProcessAiImage::dispatchSync($aiRequest);

        $this->assertDatabaseHas('ai_requests', [
            'id' => $aiRequest->id,
            'status' => 'failed',
            'error_message' => 'API Error',
            'raw_error_response' => 'Raw error details',
        ]);
    }

    public function test_job_handles_exceptions_gracefully()
    {
        $user = User::factory()->create();
        $aiRequest = AiRequest::factory()->create(['user_id' => $user->id, 'status' => 'pending']);

        $this->mock(GoogleAiService::class, function (MockInterface $mock) {
            $mock->shouldReceive('analyzeImage')->once()->andThrow(new \Exception('A severe exception occurred'));
        });

        ProcessAiImage::dispatchSync($aiRequest);

        $this->assertDatabaseHas('ai_requests', [
            'id' => $aiRequest->id,
            'status' => 'failed',
            'error_message' => 'A severe exception occurred',
        ]);
    }

    public function test_job_sets_status_to_processing_at_start()
    {
        $user = User::factory()->create();
        $aiRequest = AiRequest::factory()->create(['user_id' => $user->id, 'status' => 'pending']);
        $fakeResponse = ['success' => true, 'data' => ['some' => 'data']];

        $this->mock(GoogleAiService::class, function (MockInterface $mock) use ($aiRequest, $fakeResponse) {
            $mock->shouldReceive('analyzeImage')->once()->andReturnUsing(function () use ($aiRequest, $fakeResponse) {
                // At the moment of execution, the status should be 'processing'
                $this->assertEquals('processing', $aiRequest->fresh()->status);
                return $fakeResponse;
            });
        });

        ProcessAiImage::dispatchSync($aiRequest);

        // Final status should be completed
        $this->assertEquals('completed', $aiRequest->fresh()->status);
    }
}
