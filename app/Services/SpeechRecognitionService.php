<?php

namespace App\Services;

class SpeechRecognitionService
{
    /**
     * Распознаёт речь из аудиофайла через Whisper.
     *
     * @param string $audioPath Относительный путь к аудио (например, "audio/test.mp3")
     * @param string $language  Код языка (по умолчанию 'en' для английского)
     * @return string Распознанный текст с пунктуацией
     */
    public function recognize(string $audioPath, string $language = 'en'): string
    {
        // Полный путь к аудиофайлу
        $fullPath = storage_path('app/public/' . $audioPath);

        if (!file_exists($fullPath)) {
            return '[ошибка: файл не найден]';
        }

        $script = base_path('scripts/recognize.py');
        $python = env('PYTHON_PATH', 'python');

        // Формируем команду (пути в кавычках — защита от пробелов)
        $command = "\"{$python}\" \"{$script}\" \"{$fullPath}\" 2>&1";
        $output = shell_exec($command);

        if (empty($output)) {
            return '[ошибка распознавания: пустой ответ]';
        }

        // Извлекаем JSON из вывода (там могут быть логи Whisper)
        $result = $this->extractJson($output);

        if ($result === null || !isset($result['text'])) {
            return '[ошибка распознавания: некорректный ответ]';
        }

        return $result['text'];
    }

    /**
     * Извлекает JSON из вывода скрипта.
     * Whisper и другие библиотеки пишут логи в stderr, поэтому
     * итоговый JSON может быть последней строкой.
     */
    private function extractJson(string $output): ?array
    {
        $lines = explode("\n", trim($output));

        // Идём с конца — JSON обычно в последней строке
        for ($i = count($lines) - 1; $i >= 0; $i--) {
            $line = trim($lines[$i]);
            if (str_starts_with($line, '{') && str_ends_with($line, '}')) {
                $decoded = json_decode($line, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    return $decoded;
                }
            }
        }

        return null;
    }
}