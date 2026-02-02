<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\Response;

class FetchMarsWeatherToCsv extends Command
{
    protected string $baseUrl = 'https://api.maas2.apollorion.com';
    protected $signature = 'app:fetch-mars-weather-to-csv';
    protected $description = 'Command description';

    /**
     * @return void
     * @throws ConnectionException
     */
    public function handle(): void
    {
        $lastSol = $this->getLastSolNumber();

        $progressBar = $this->output->createProgressBar($lastSol);
        $progressBar->start();

        for ($solNumber = 1; $solNumber < $lastSol; $solNumber++) {
            $progressBar->advance();

            if ($this->solAlreadySaved($solNumber)) continue;

            $solData = $this->getWeatherBySol($solNumber);

            usleep(500000);

            if (is_null($solData)) continue;

            $this->saveSolToCsv($solData);
        }

        $progressBar->finish();
    }

    /**
     * @return int
     * @throws ConnectionException
     */
    protected function getLastSolNumber(): int
    {
        return (int)Http::get($this->baseUrl)->json()['sol'];
    }

    /**
     * @param int $solId
     * @return array|null
     */
    protected function getWeatherBySol(int $solId): ?array
    {
        $attempt = 0;
        $maxAttempts = 2; // макс. попыток при проблемах с соединением

        do {
            try {
                $response = Http::timeout(30)->get("$this->baseUrl/$solId");

                // Проверяем статус вручную
                if ($response->status() === Response::HTTP_NOT_FOUND) {
                    return null; // нет данных для этого сола
                }

                if ($response->failed()) {
                    $this->warn("Ошибка {$response->status()} для SOL $solId");
                    return null;
                }

                return $response->json(); // успешный ответ

            } catch (ConnectionException $e) {
                $attempt++;
                $wait = 5 * $attempt; // увеличиваем паузу при каждой попытке (5, 10, 15, ...)
                $this->warn("Проблема соединения для SOL $solId, ждем {$wait} секунд и пробуем снова...");
                sleep($wait);
            }

        } while ($attempt < $maxAttempts);

        $this->warn("SOL $solId пропущен после $maxAttempts попыток соединения.");
        return null;
    }


    /**
     * @param array $solData
     * @return void
     */
    function saveSolToCsv(array $solData): void
    {
        $file = database_path('seeders/csv/mars_weather.csv');
        $writeHeader = !file_exists($file);
        $fp = fopen($file, 'a');

        if ($writeHeader) {
            fputcsv($fp, [
                'sol', 'earth_date', 'ls', 'season',
                'min_temp', 'max_temp', 'pressure',
                'atmo_opacity', 'sunrise', 'sunset'
            ]);
        }

        fputcsv($fp, [
            $solData['sol'],
            $solData['terrestrial_date'],
            $solData['ls'],
            $solData['season'],
            is_numeric($solData['min_temp']) ? $solData['min_temp'] : null,
            is_numeric($solData['max_temp']) ? $solData['max_temp'] : null,
            $solData['pressure'],
            $solData['atmo_opacity'],
            $solData['sunrise'],
            $solData['sunset']
        ]);

        fclose($fp);
    }

    /**
     * @param int $sol
     * @return bool
     */
    private function solAlreadySaved(int $sol): bool
    {
        if (!file_exists('mars_weather.csv')) return false;

        $file = fopen('mars_weather.csv', 'r');
        while (($row = fgetcsv($file)) !== false) {
            if ($row[0] == $sol) {
                fclose($file);
                return true;
            }
        }
        fclose($file);
        return false;
    }
}
