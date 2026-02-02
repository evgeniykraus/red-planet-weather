<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class MarsWeather extends Model
{
    protected $table = 'mars_weathers';

    protected $fillable = [
        'sol',
        'earth_date',
        'ls',
        'mars_month',
        'season',
        'min_temp',
        'max_temp',
        'pressure',
        'atmo_opacity',
        'sunrise',
        'sunset',
    ];

    protected function casts(): array
    {
        return [
            'sol' => 'integer',
            'earth_date' => 'date',
            'ls' => 'integer',
            'mars_month' => 'integer',
            'min_temp' => 'integer',
            'max_temp' => 'integer',
            'pressure' => 'float',
        ];
    }

    protected function marsMonth(): Attribute
    {
        return Attribute::make(
            get: fn() => (int) floor($this->ls / 30) + 1,
        );
    }
}
