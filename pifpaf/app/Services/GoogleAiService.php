<?php

namespace App\Services;

use Gemini;
use Gemini\Enums\MimeType;
use Gemini\Data\Content;
use Gemini\Data\Part;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class GoogleAiService
{
    /**
     * Analyse une image et retourne les données structurées.
     *
     * @param string $imagePath
     * @return array|null
     */
    public function analyzeImage(string $imagePath): ?array
    {
        try {
            Log::info('Starting image analysis with Google AI.');
            $apiKey = config('services.gemini.api_key');
            Log::info('API Key loaded.');

            if (!$apiKey) {
                Log::critical('GEMINI_API_KEY is not configured in services.php or .env file.');
                return null;
            }

            if (!File::exists($imagePath)) {
                Log::error("Image file not found at path: {$imagePath}");
                return null;
            }

            $prompt = <<<'EOT'
            Analyze the image of a second-hand item and provide a JSON object with the following details:
            - "title": A compelling and descriptive title for the item.
            - "description": A detailed description of the item, including its condition, features, and potential use cases.
            - "category": Suggest a category from this list: 'Vêtements', 'Électronique', 'Maison', 'Sport', 'Loisirs', 'Autre'.
            - "price": A suggested price in EUR (float).

            The JSON object should be the only output.
            EOT;

            Log::info('Sending request to Gemini API.');
            $client = Gemini::client($apiKey);

            $response = $client->geminiProVision()
                ->generateContent([
                    Part::text($prompt),
                    Part::blob(
                        mimeType: MimeType::IMAGE_JPEG,
                        data: file_get_contents($imagePath)
                    )
                ]);

            $result = $response->text();
            Log::info('Received response from Gemini API: ' . $result);

            // Clean the response to get a valid JSON
            $jsonResponse = trim(str_replace(['```json', '```'], '', $result));
            $data = json_decode($jsonResponse, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::error('Failed to decode JSON from Gemini API response.', [
                    'response' => $result,
                    'json_error' => json_last_error_msg(),
                ]);
                return null;
            }

            Log::info('Successfully parsed Gemini API response.');
            return $data;
        } catch (\Exception $e) {
            Log::error('An error occurred while calling the Gemini API.', [
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }
}
