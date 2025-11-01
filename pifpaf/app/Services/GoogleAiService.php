<?php

namespace App\Services;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class GoogleAiService
{
    /**
     * Analyse une image pour un ou plusieurs objets et retourne les données structurées.
     *
     * @param string $imagePath
     * @return array|null
     */
    public function analyzeImage(string $imagePath): ?array
    {
        try {
            Log::info('Starting multi-object image analysis with Google AI.');
            $apiKey = config('services.gemini.api_key');

            if (!$apiKey) {
                Log::critical('GEMINI_API_KEY is not configured.');
                return null;
            }

            if (!File::exists($imagePath)) {
                Log::error("Image file not found at path: {$imagePath}");
                return null;
            }

            $prompt = <<<'EOT'
            Analyze the image to identify all distinct second-hand items suitable for individual sale. For each item found, provide a JSON object with the following details:
            - "title": A compelling and descriptive title for the item in French.
            - "description": A detailed description of the item in French, including its condition, features, and potential use cases.
            - "category": Suggest a category from this list: 'Vêtements', 'Électronique', 'Maison', 'Sport', 'Loisirs', 'Autre'.
            - "price": A suggested price in EUR (float).
            - "box": A bounding box object with normalized coordinates (from 0.0 to 1.0) for the item's location in the image, defined by four points: {"x1": top-left-x, "y1": top-left-y, "x2": bottom-right-x, "y2": bottom-right-y}.

            The final output must be a single JSON array containing one object for each identified item. If only one item is found, return an array with a single object. If no items are found, return an empty array.
            EOT;

            $result = $this->geminiVisionRequest($apiKey, $prompt, $imagePath);

            if (!$result) {
                return null;
            }

            Log::info('Received response from Gemini API for multi-object analysis.');

            $jsonResponse = trim(str_replace(['```json', '```'], '', $result));
            $data = json_decode($jsonResponse, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::error('Failed to decode JSON from Gemini API response.', [
                    'response' => $result,
                    'json_error' => json_last_error_msg(),
                ]);
                return null;
            }

            Log::info('Successfully parsed Gemini API response for multi-object analysis.');
            return $data;
        } catch (\Exception $e) {
            Log::error('An unexpected error occurred while calling the Gemini API.', [
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Exécute un appel à l'API Gemini Vision via cURL.
     *
     * @param string $apiKey
     * @param string $prompt
     * @param string $imagePath
     * @return string|null Le texte de la réponse ou null en cas d'erreur.
     */
    private function geminiVisionRequest(string $apiKey, string $prompt, string $imagePath): ?string
    {
        $apiUrl = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key={$apiKey}";

        $mimeType = mime_content_type($imagePath);
        $imageData = base64_encode(file_get_contents($imagePath));

        $body = [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $prompt],
                        [
                            'inline_data' => [
                                'mime_type' => $mimeType,
                                'data' => $imageData,
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $jsonBody = json_encode($body);

        $ch = curl_init($apiUrl);
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $jsonBody,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
            ],
            CURLOPT_TIMEOUT => 30,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($curlError) {
            Log::error('cURL error during Gemini API call.', ['error' => $curlError]);
            return null;
        }

        if ($httpCode !== 200) {
            $data = json_decode($response, true);
            $errorMessage = $data['error']['message'] ?? 'Unknown API error';
            Log::error('Gemini API returned an error.', [
                'http_code' => $httpCode,
                'error_message' => $errorMessage,
                'response_body' => $response,
            ]);
            return null;
        }

        $data = json_decode($response, true);
        $text = $data['candidates'][0]['content']['parts'][0]['text'] ?? null;

        if (!$text) {
            Log::error('Malformed response from Gemini API.', ['response' => $response]);
            return null;
        }

        return $text;
    }
}
