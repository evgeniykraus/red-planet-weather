<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AresArchiveTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();
    }

    public function test_ares_archive_page_loads_successfully(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('AresArchive')
            ->has('availableMonths')
        );
    }

    public function test_ares_archive_page_has_available_months(): void
    {
        $response = $this->get('/');

        $response->assertInertia(fn ($page) => $page
            ->where('availableMonths', range(1, 12))
        );
    }

    public function test_ares_archive_statistics_loads_successfully(): void
    {
        $response = $this->get('/statistics?mars_month=1');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('AresArchive')
            ->has('statistics')
            ->where('selectedMonth', 1)
        );
    }

    public function test_ares_archive_statistics_has_correct_structure(): void
    {
        $response = $this->get('/statistics?mars_month=1');

        $response->assertInertia(fn ($page) => $page
            ->has('statistics.month')
            ->has('statistics.month_name')
            ->has('statistics.statistics.average_temp')
            ->has('statistics.statistics.absolute_min')
            ->has('statistics.statistics.absolute_max')
        );
    }

    public function test_ares_archive_works_with_get_parameter(): void
    {
        $response = $this->get('/?mars_month=3');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('AresArchive')
            ->where('selectedMonth', 3)
            ->has('statistics')
            ->where('statistics.month', 3)
        );
    }

    public function test_ares_archive_ignores_invalid_month(): void
    {
        $response = $this->get('/?mars_month=15');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('AresArchive')
            ->missing('statistics')
            ->where('selectedMonth', null)
        );
    }
}
