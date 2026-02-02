<?php

namespace App\Repositories\Contracts;

use Illuminate\Support\Collection;

interface MarsWeatherRepositoryInterface
{
    public function getMonthStatistics(int $marsMonth): ?array;

    public function getAllMonths(): Collection;
}
