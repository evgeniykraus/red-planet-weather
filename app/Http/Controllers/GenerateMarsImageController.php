<?php

namespace App\Http\Controllers;

use App\Http\Requests\GenerateMarsImageRequest;
use App\Services\ImageGenerationService;
use App\Services\MarsWeatherService;
use Illuminate\Http\RedirectResponse;

class GenerateMarsImageController extends Controller
{
    public function __construct(
        private readonly ImageGenerationService $imageService,
        private readonly MarsWeatherService $weatherService,
    ) {}

    /**
     * Handle the incoming request.
     */
    public function __invoke(GenerateMarsImageRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $marsMonth = (int) $validated['mars_month'];

        // Получить погодные данные для месяца
        $weatherData = $this->weatherService->getMonthStatistics($marsMonth);

        // Генерировать изображение
        $result = $this->imageService->generateMarsImage($marsMonth, $weatherData);

        // Редирект обратно на главную страницу с данными о сгенерированном изображении
        return redirect()->route('ares.archive')->with([
            'generatedImage' => $result,
            'marsMonth' => $marsMonth,
        ]);
    }
}
