<?php

namespace App\Repositories;

use App\Repositories\Contracts\MarsWeatherRepositoryInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class MarsWeatherRepository implements MarsWeatherRepositoryInterface
{
    public function getMonthStatistics(int $marsMonth): ?array
    {
        $result = DB::table('mars_weathers')
            ->selectRaw('
                AVG((min_temp + max_temp) / 2.0) as average_temp,
                MIN(min_temp) as absolute_min,
                MAX(max_temp) as absolute_max,
                COUNT(*) as total_sols,
                MIN(earth_date) as first_date,
                MAX(earth_date) as last_date,
                MIN(ls) as min_ls,
                MAX(ls) as max_ls
            ')
            ->where('mars_month', $marsMonth)
            ->whereNotNull('min_temp')
            ->whereNotNull('max_temp')
            ->first();

        if (!$result || $result->total_sols === 0) {
            return null;
        }

        return (array) $result;
    }

    public function getAllMonths(): Collection
    {
        return DB::table('mars_weathers')
            ->select('mars_month')
            ->distinct()
            ->orderBy('mars_month')
            ->pluck('mars_month');
    }
}
