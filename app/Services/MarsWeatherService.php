<?php

namespace App\Services;

use App\Repositories\Contracts\MarsWeatherRepositoryInterface;
use Carbon\Carbon;

readonly class MarsWeatherService
{
    public function __construct(
        private MarsWeatherRepositoryInterface $marsWeatherRepository
    )
    {
    }

    public function getMonthStatistics(int $marsMonth): ?array
    {
        $rawData = $this->marsWeatherRepository->getMonthStatistics($marsMonth);

        if (!$rawData) {
            return null;
        }

        $monthInfo = $this->getMonthInfo($marsMonth);

        return [
            'month' => $marsMonth,
            'month_name' => "Месяц {$marsMonth}",
            'ls_range' => $monthInfo['ls_range'],
            'season' => $monthInfo['season'],
            'season_description' => $monthInfo['season_description'],
            'statistics' => [
                'average_temp' => round($rawData['average_temp'], 1),
                'absolute_min' => (int)$rawData['absolute_min'],
                'absolute_max' => (int)$rawData['absolute_max'],
                'total_sols' => (int)$rawData['total_sols'],
            ],
            'date_range' => [
                'first_date' => $this->formatDateRussian($rawData['first_date']),
                'last_date' => $this->formatDateRussian($rawData['last_date']),
                'first_date_raw' => $rawData['first_date'],
                'last_date_raw' => $rawData['last_date'],
            ],
            'ls_actual' => [
                'min' => (int)$rawData['min_ls'],
                'max' => (int)$rawData['max_ls'],
            ],
        ];
    }

    public function getMonthInfo(int $marsMonth): array
    {
        $lsStart = ($marsMonth - 1) * 30;
        $lsEnd = $marsMonth * 30;

        $seasonMap = [
            1 => ['name' => 'Весна', 'description' => 'Ранняя весна (северное полушарие)'],
            2 => ['name' => 'Весна', 'description' => 'Середина весны (северное полушарие)'],
            3 => ['name' => 'Весна', 'description' => 'Поздняя весна (северное полушарие)'],
            4 => ['name' => 'Лето', 'description' => 'Раннее лето (северное полушарие)'],
            5 => ['name' => 'Лето', 'description' => 'Середина лета (северное полушарие)'],
            6 => ['name' => 'Лето', 'description' => 'Позднее лето (северное полушарие)'],
            7 => ['name' => 'Осень', 'description' => 'Ранняя осень (северное полушарие)'],
            8 => ['name' => 'Осень', 'description' => 'Середина осени (северное полушарие)'],
            9 => ['name' => 'Осень', 'description' => 'Поздняя осень (северное полушарие)'],
            10 => ['name' => 'Зима', 'description' => 'Ранняя зима (северное полушарие)'],
            11 => ['name' => 'Зима', 'description' => 'Середина зимы (северное полушарие)'],
            12 => ['name' => 'Зима', 'description' => 'Поздняя зима (северное полушарие)'],
        ];

        $seasonInfo = $seasonMap[$marsMonth] ?? ['name' => 'Неизвестно', 'description' => 'Неизвестный сезон'];

        return [
            'ls_range' => "Ls {$lsStart}°-{$lsEnd}°",
            'ls_start' => $lsStart,
            'ls_end' => $lsEnd,
            'season' => $seasonInfo['name'],
            'season_description' => $seasonInfo['description'],
        ];
    }

    public function getAllAvailableMonths(): array
    {
        return $this->marsWeatherRepository->getAllMonths()->toArray();
    }

    /**
     * Format date in Russian format: "1 августа 2013"
     */
    private function formatDateRussian(string $date): string
    {
        return Carbon::parse($date)->locale('ru')->isoFormat('D MMMM Y');
    }
}
