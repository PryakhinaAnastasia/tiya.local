<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Task;
use PhpOffice\PhpSpreadsheet\IOFactory;

class TaskSeeder extends Seeder
{
    public function run(): void
    {
        $xlsxPath = base_path('tasks.xlsx');

        if (!file_exists($xlsxPath)) {
            $this->command->error('Файл tasks.xlsx не найден в корне проекта!');
            return;
        }

        // Загружаем Excel
        $spreadsheet = IOFactory::load($xlsxPath);
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray();

        // Пропускаем заголовок (первая строка)
        array_shift($rows);

        $count = 0;
        foreach ($rows as $row) {
            // Проверяем, что есть хотя бы type и title
            if (empty($row[0]) || empty($row[1])) {
                continue;
            }

            Task::create([
                'type' => (int) $row[0],
                'title' => $row[1],
                'topic' => $row[2] ?? null,
                'difficulty' => isset($row[3]) && $row[3] !== '' ? (int) $row[3] : 1,
                'image_file' => $row[4] ?? null,
                'image_file_2' => $row[5] ?? null,
                'audio_file' => $row[6] ?? null,
                'description' => $row[7] ?? '',
            ]);

            $count++;
        }

        $this->command->info("Загружено {$count} заданий.");
    }
}
