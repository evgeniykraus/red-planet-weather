<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MarsWeatherTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed some test data
        $this->artisan('db:seed', ['--class' => 'MarsWeatherSeeder']);
    }

    public function test_index_page_loads(): void
    {
        $response = $this->get(route('mars.index'));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('MarsWeather')
            ->has('availableMonths', 12)
        );
    }

    public function test_statistics_page_returns_data(): void
    {
        $response = $this->get(route('mars.statistics', ['mars_month' => 5]));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('MarsWeather')
            ->where('selectedMonth', 5)
            ->has('statistics')
            ->where('statistics.month', 5)
            ->where('statistics.season', 'Лето')
            ->has('statistics.statistics')
        );
    }

    public function test_invalid_month_returns_validation_error(): void
    {
        $response = $this->get(route('mars.statistics', ['mars_month' => 13]));

        $response->assertSessionHasErrors('mars_month');
    }

    public function test_missing_month_returns_validation_error(): void
    {
        $response = $this->get(route('mars.statistics'));

        $response->assertSessionHasErrors('mars_month');
    }
}
