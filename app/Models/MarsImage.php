<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class MarsImage extends Model
{
    protected $fillable = [
        'mars_month',
        'image_path',
        'interpretation',
        'weather_context',
    ];

    protected function casts(): array
    {
        return [
            'mars_month' => 'integer',
            'weather_context' => 'array',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    protected function imageUrl(): Attribute
    {
        return Attribute::make(
            get: fn () => Storage::url($this->image_path)
        );
    }
}
