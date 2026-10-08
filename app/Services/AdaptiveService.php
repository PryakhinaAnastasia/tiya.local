<?php

namespace App\Services;

use App\Models\Progress;
use App\Models\Task;

class AdaptiveService
{
    public function getNextTask(int $userId): ?Task
    {
        $lastProgress = Progress::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->first();

        if (!$lastProgress) {
            return Task::orderBy('difficulty', 'asc')->first();
        }

        $lastScore = $lastProgress->score;

        if ($lastScore < 50) {
            return Task::where('difficulty', '<', $lastProgress->task->difficulty)
                ->orderBy('difficulty', 'desc')
                ->first();
        } elseif ($lastScore > 80) {
            return Task::where('difficulty', '>', $lastProgress->task->difficulty)
                ->orderBy('difficulty', 'asc')
                ->first();
        }

        return Task::orderBy('difficulty', 'asc')->first();
    }
}