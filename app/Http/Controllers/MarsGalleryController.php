<?php

namespace App\Http\Controllers;

use App\Repositories\Contracts\MarsImageRepositoryInterface;
use Inertia\Inertia;
use Inertia\Response;

class MarsGalleryController extends Controller
{
    public function __construct(
        private readonly MarsImageRepositoryInterface $marsImageRepository,
    ) {}

    public function index(): Response
    {
        $images = $this->marsImageRepository->getAllImages();

        // Группировать по месяцам для фильтров
        $monthsWithImages = $images->pluck('mars_month')->unique()->sort()->values();

        return Inertia::render('MarsGallery', [
            'images' => $images->map(fn ($image) => [
                'id' => $image->id,
                'mars_month' => $image->mars_month,
                'image_url' => $image->image_url,
                'interpretation' => $image->interpretation,
                'weather_context' => $image->weather_context,
                'created_at' => $image->created_at->toISOString(),
            ]),
            'availableMonths' => range(1, 12),
            'monthsWithImages' => $monthsWithImages,
        ]);
    }
}
