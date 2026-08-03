<?php

namespace Tests\Feature\Research;

use App\Models\Research;
use App\Models\User;
use App\Services\AdminDashboardService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AnalyticsTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_dashboard_service_generates_valid_metrics_and_chart_data(): void
    {
        $service = app(AdminDashboardService::class);

        $metrics = $service->getMetrics();
        $pubGrowth = $service->getMonthlyPublicationGrowth();
        $dlGrowth = $service->getMonthlyDownloadGrowth();

        $this->assertArrayHasKey('total_publications', $metrics);
        $this->assertCount(6, $pubGrowth['labels']);
        $this->assertCount(6, $pubGrowth['data']);
        $this->assertCount(6, $dlGrowth['data']);
    }

    public function test_researcher_analytics_dashboard_renders_stats(): void
    {
        $user = User::factory()->create();

        Research::create([
            'user_id' => $user->id,
            'title' => 'Analytics Paper',
            'slug' => 'analytics-paper',
            'abstract' => 'Sample abstract for analytics feature test.',
            'pdf_path' => 'research_pdfs/sample.pdf',
            'views' => 100,
            'downloads' => 30,
            'status' => 'published',
        ]);

        $response = $this->actingAs($user)->get('/dashboard/analytics');

        $response->assertStatus(200);
        $response->assertSee('100'); // Total views
        $response->assertSee('30');  // Total downloads
    }
}
