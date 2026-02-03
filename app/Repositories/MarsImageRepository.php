<?php

namespace App\Repositories;

use App\Models\MarsImage;
use App\Repositories\Contracts\MarsImageRepositoryInterface;
use Illuminate\Support\Collection;

class MarsImageRepository implements MarsImageRepositoryInterface
{
    public function create(array $data): MarsImage
    {
        return MarsImage::create($data);
    }

    public function getAllImages(): Collection
    {
        return MarsImage::query()
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getImagesByMonth(int $month): Collection
    {
        return MarsImage::query()
            ->where('mars_month', $month)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getLatestImages(int $limit = 20): Collection
    {
        return MarsImage::query()
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }
}
