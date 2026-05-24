<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OpenAiImageService
{
    /**
     * @return list<string> image URLs (temporary; download to disk when user picks one)
     */
    public function generateMenuCovers(string $prompt, int $count = 3): array
    {
        $key = config('services.openai.key');
        if (! $key) {
            return [];
        }

        $model = config('services.openai.image_model', 'dall-e-3');
        $urls = [];

        for ($i = 0; $i < max(1, min(4, $count)); $i++) {
            try {
                $suffix = $i > 0 ? ' (variation '.($i + 1).', professional food photography)' : '';
                $response = Http::withToken($key)
                    ->timeout(120)
                    ->post('https://api.openai.com/v1/images/generations', [
                        'model' => $model,
                        'prompt' => mb_substr($prompt.$suffix, 0, 900),
                        'n' => 1,
                        'size' => '1024x1024',
                    ]);

                if ($response->successful()) {
                    $url = $response->json('data.0.url');
                    if (is_string($url) && str_starts_with($url, 'http')) {
                        $urls[] = $url;
                    }
                } else {
                    Log::warning('OpenAI images error', ['body' => $response->body()]);
                }
            } catch (\Throwable $e) {
                Log::warning('OpenAI image request failed', ['e' => $e->getMessage()]);
            }
        }

        return array_values(array_filter($urls));
    }
}
