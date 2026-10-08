<?php

namespace App\Services;

class SpeechRecognitionService
{
    public function recognize(string $audioPath, string $language = 'en'): string
    {
        $fullPath = storage_path('app/public/' . $audioPath);
        $modelPath = base_path('models/vosk-model-' . ($language === 'en' ? 'en-us-0.22' : 'ru-0.22'));

        $script = base_path('scripts/recognize.py');
        $python = 'C:\Users\Nastya\AppData\Local\Programs\Python\Python310\python.exe';

        $command = "\"{$python}\" \"{$script}\" \"{$fullPath}\" \"{$modelPath}\" 2>&1";
        $output = shell_exec($command);

        $result = json_decode($output, true);

        return $result['text'] ?? '[ошибка распознавания]';
    }
}