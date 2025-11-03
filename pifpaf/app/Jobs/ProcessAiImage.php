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
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 150;

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

            $response = $googleAiService->analyzeImage($imagePath);

            if ($response['success']) {
                $this->aiRequest->update([
                    'status' => 'completed',
                    'result' => $response['data'],
                    'error_message' => null,
                    'raw_error_response' => null,
                ]);
            } else {
                Log::warning('Analyse IA échouée. Enregistrement de l\'erreur.', [
                    'ai_request_id' => $this->aiRequest->id,
                    'error' => $response['error'] ?? 'Aucun résultat.',
                ]);
                $this->aiRequest->update([
                    'status' => 'failed',
                    'error_message' => $response['error'] ?? 'L\'analyse de l\'image n\'a retourné aucun résultat.',
                    'raw_error_response' => $response['raw_response'] ?? null,
                ]);
                Log::info('Erreur enregistrée pour AiRequest.', ['ai_request_id' => $this->aiRequest->id]);
            }
        } catch (Throwable $e) {
            Log::error('Exception interceptée dans ProcessAiImage.', [
                'ai_request_id' => $this->aiRequest->id,
                'error' => $e->getMessage(),
            ]);
            $this->aiRequest->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
                'raw_error_response' => $e->getTraceAsString(),
            ]);
            Log::info('Erreur d\'exception enregistrée pour AiRequest.', ['ai_request_id' => $this->aiRequest->id]);
        }
    }
}
