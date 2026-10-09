<?php

namespace App\Services;

class AudioService
{
    /**
     * Сохраняет загруженное аудио в storage/app/public/audio/
     */
    public function saveAudio($file): string
    {
        return $file->store('audio', 'public');
    }

    /**
     * Шумоподавление через RNNoise.
     *
     * ⚠️ ВАЖНО: Автоматически НЕ применяется, потому что для записей
     * с музыкальным сопровождением RNNoise снижает разборчивость речи
     * (режет начало слов).
     *
     * Этот метод остаётся в проекте как отдельный модуль. Он может быть
     * вызван опционально — например, из панели преподавателя для обработки
     * шумных домашних записей учеников.
     *
     * @param string $path Относительный путь к аудио
     * @return string Путь к очищенному файлу (или исходный, если не удалось)
     */
    public function applyNoiseReduction(string $path): string
    {
        $fullPath = storage_path('app/public/' . $path);

        if (!file_exists($fullPath)) {
            return $path;
        }

        $outputPath = storage_path('app/public/audio/clean_' . basename($path));

        $script = base_path('scripts/denoise.py');
        $python = env('PYTHON_PATH', 'python');

        $command = "\"{$python}\" \"{$script}\" \"{$fullPath}\" \"{$outputPath}\" 2>&1";
        shell_exec($command);

        if (file_exists($outputPath)) {
            return 'audio/clean_' . basename($path);
        }

        return $path;
    }
}