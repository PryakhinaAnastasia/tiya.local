<?php

namespace App\Services;

class AudioService
{
    public function saveAudio($file): string
    {
        return $file->store('audio', 'public');
    }

    public function applyNoiseReduction(string $path): string
    {
        $fullPath = storage_path('app/public/' . $path);
        $outputPath = storage_path('app/public/audio/clean_' . basename($path));

        $script = base_path('scripts/denoise.py');
        $command = "python {$script} {$fullPath} {$outputPath} 2>&1";
        shell_exec($command);

        if (file_exists($outputPath)) {
            return 'audio/clean_' . basename($path);
        }

        return $path;
    }
}