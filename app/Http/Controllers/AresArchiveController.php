<?php

namespace App\Http\Controllers;

use App\Http\Requests\MonthStatisticsRequest;
use App\Services\MarsWeatherService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AresArchiveController extends Controller
{
    public function __construct(
        private readonly MarsWeatherService $marsWeatherService
    ) {}

    public function index(Request $request): Response
    {
        $data = [
            'availableMonths' => range(1, 12),
            'generatedImage' => session('generatedImage'),
            'selectedMonth' => session('marsMonth'),
        ];

        // Если передан параметр mars_month, получаем статистику
        if ($request->has('mars_month')) {
            $marsMonth = (int) $request->input('mars_month');

            // Валидация
            if ($marsMonth >= 1 && $marsMonth <= 12) {
                $statistics = $this->marsWeatherService->getMonthStatistics($marsMonth);
                $data['statistics'] = $statistics;
                $data['selectedMonth'] = $marsMonth;
            }
        }

        return Inertia::render('AresArchive', $data);
    }

    public function getMonthStatistics(MonthStatisticsRequest $request): Response
    {
        $validated = $request->validated();
        $marsMonth = (int) $validated['mars_month'];
        $statistics = $this->marsWeatherService->getMonthStatistics($marsMonth);

        return Inertia::render('AresArchive', [
            'statistics' => $statistics,
            'selectedMonth' => $marsMonth,
            'availableMonths' => range(1, 12),
        ]);
    }
}
