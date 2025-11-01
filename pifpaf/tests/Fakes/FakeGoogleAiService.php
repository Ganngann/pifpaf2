<?php

namespace Tests\Fakes;

use App\Services\GoogleAiService;

class FakeGoogleAiService extends GoogleAiService
{
    /**
     * The response that should be returned by the next call to analyzeImage.
     *
     * @var array|null
     */
    public static $nextResponse = null;

    /**
     * Override the real method to return the fake response.
     *
     * @param string $imagePath
     * @return array|null
     */
    public function analyzeImage(string $imagePath): ?array
    {
        return static::$nextResponse;
    }
}
