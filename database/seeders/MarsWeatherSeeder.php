<?php

namespace Database\Seeders;

use App\Models\MarsWeather;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class MarsWeatherSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Путь к файлу
        $filePath = database_path('seeders/csv/mars_weather.csv');

        if (! file_exists($filePath)) {
            $this->command->error("Файл не найден по пути: $filePath");

            return;
        }

        // 2. Открываем поток чтения
        $file = fopen($filePath, 'r');

        // Пропускаем заголовок CSV
        fgetcsv($file);

        $chunkSize = 1000; // Размер пачки для вставки
        $data = [];
        $now = Carbon::now();

        $this->command->info('Начинаем импорт данных...');

        while (($row = fgetcsv($file)) !== false) {
            // Парсинг строки (обрабатываем "мусор" типа "--")
            $ls = (int) $row[2];
            $marsMonth = (int) floor($ls / 30) + 1;

            $data[] = [
                'sol' => (int) $row[0],
                'earth_date' => $row[1],
                'ls' => $ls,
                'mars_month' => $marsMonth,
                'season' => $row[3],
                'min_temp' => $this->cleanNumber($row[4]),
                'max_temp' => $this->cleanNumber($row[5]),
                'pressure' => $this->cleanNumber($row[6]),
                'atmo_opacity' => $row[7],
                'sunrise' => $row[8],
                'sunset' => $row[9],
                'created_at' => $now, // При insert/upsert таймстампы не ставятся сами
                'updated_at' => $now,
            ];

            // Когда набрали пачку - отправляем в БД
            if (count($data) >= $chunkSize) {
                $this->insertData($data);
                $data = []; // Очищаем буфер
                $this->command->getOutput()->write('.'); // Прогресс-бар точками
            }
        }

        // Дописываем остатки, если они есть
        if (! empty($data)) {
            $this->insertData($data);
        }

        fclose($file);
        $this->command->newLine();
        $this->command->info('Импорт завершен!');
    }

    /**
     * Логика массовой вставки (Upsert)
     */
    private function insertData(array $data): void
    {
        // upsert(массив данных, [уникальные поля], [какие поля обновлять при совпадении])
        MarsWeather::query()->upsert(
            $data,
            ['sol'], // Уникальный ключ. Если sol совпадает -> update, иначе insert
            [
                'earth_date',
                'ls',
                'mars_month',
                'season',
                'min_temp',
                'max_temp',
                'pressure',
                'atmo_opacity',
                'sunrise',
                'sunset',
                'updated_at',
            ]
        );
    }

    private function cleanNumber(?string $value): ?string
    {
        if ($value === null || $value === '' || $value === '--') {
            return null;
        }

        return $value;
    }
}
