<?php

namespace App\Repositories\Contracts;

use App\Models\MarsImage;
use Illuminate\Support\Collection;

interface MarsImageRepositoryInterface
{
    public function create(array $data): MarsImage;

    public function getAllImages(): Collection;

    public function getImagesByMonth(int $month): Collection;

    public function getLatestImages(int $limit = 20): Collection;
}
