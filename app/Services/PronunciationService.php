<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class PronunciationService
{
    public function evaluate(string $audioPath): array
    {
        $fullPath = storage_path('app/public/' . $audioPath);
        $apiKey = config('services.speechace.key');

        if (!$apiKey) {
            return ['score' => 0, 'message' => 'Speechace API ключ не настроен'];
        }

        $apiUrl = 'https://api.speechace.co/api/scoring/text/v9/json';

        $response = Http::attach(
            'user_audio_file',
            file_get_contents($fullPath),
            basename($fullPath)
        )->post($apiUrl, [
            'key' => $apiKey,
            'dialect' => 'en-us',
            'text' => 'placeholder',
        ]);

        if ($response->successful()) {
            return $response->json();
        }

        return ['score' => 0, 'message' => 'Ошибка Speechace'];
    }
}