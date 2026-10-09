<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class GrammarCheckService
{
    public function check(string $text): array
    {
        try {
            $response = Http::timeout(15)->post('https://api.languagetool.org/v2/check', [
                'text' => $text,
                'language' => 'en-US',
            ]);

            if ($response->successful()) {
                return $response->json() ?? ['matches' => []];
            }

            return ['matches' => [], 'error' => 'LanguageTool вернул ошибку: ' . $response->status()];

        } catch (\Exception $e) {
            return ['matches' => [], 'error' => 'LanguageTool недоступен: ' . $e->getMessage()];
        }
    }

    public function calculateScore(array $grammarResult): float
    {
        $errors = $grammarResult['matches'] ?? [];
        $errorCount = count($errors);
        return max(0, 100 - ($errorCount * 5));
    }
}