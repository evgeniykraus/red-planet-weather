<?php

namespace App\Http\Controllers;

use App\Http\Requests\MonthStatisticsRequest;
use App\Services\MarsWeatherService;
use Inertia\Inertia;
use Inertia\Response;

class MarsWeatherController extends Controller
{
    public function __construct(
        private readonly MarsWeatherService $marsWeatherService
    ) {}

    public function index(): Response
    {
        return Inertia::render('MarsWeather', [
            'availableMonths' => range(1, 12),
            'generatedImage' => session('generatedImage'),
            'selectedMonth' => session('marsMonth'),
        ]);
    }

    public function getMonthStatistics(MonthStatisticsRequest $request): Response
    {
        $validated = $request->validated();
        $marsMonth = (int) $validated['mars_month'];
        $statistics = $this->marsWeatherService->getMonthStatistics($marsMonth);

        return Inertia::render('MarsWeather', [
            'statistics' => $statistics,
            'selectedMonth' => $marsMonth,
            'availableMonths' => range(1, 12),
        ]);
    }
}
