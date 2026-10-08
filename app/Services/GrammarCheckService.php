<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class GrammarCheckService
{
    public function check(string $text): array
    {
        $response = Http::post('https://api.languagetool.org/v2/check', [
            'text' => $text,
            'language' => 'en-US',
        ]);

        return $response->json();
    }

    public function calculateScore(array $grammarResult): float
    {
        $errors = $grammarResult['matches'] ?? [];
        $errorCount = count($errors);
        return max(0, 100 - ($errorCount * 5));
    }
}