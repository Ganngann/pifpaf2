<?php

namespace App\Jobs;

use App\Models\AiRequest;
use App\Services\GoogleAiService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Throwable;

class ProcessAiImage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public AiRequest $aiRequest)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(GoogleAiService $googleAiService): void
    {
        try {
            $this->aiRequest->update(['status' => 'processing']);

            $imagePath = Storage::disk('public')->path($this->aiRequest->image_path);

            $result = $googleAiService->analyzeImage($imagePath);

            if ($result) {
                $this->aiRequest->update([
                    'status' => 'completed',
                    'result' => $result,
                ]);
            } else {
                $this->aiRequest->update([
                    'status' => 'failed',
                    'error_message' => 'L\'analyse de l\'image n\'a retourné aucun résultat.',
                ]);
            }
        } catch (Throwable $e) {
            Log::error('Job ProcessAiImage a échoué', ['error' => $e->getMessage()]);
            $this->aiRequest->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);
        }
    }
}
