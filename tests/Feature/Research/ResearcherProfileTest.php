<?php

namespace Tests\Feature\Research;

use App\Models\Research;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ResearcherProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_researcher_profile_page_loads_with_statistics(): void
    {
        $researcher = User::factory()->create([
            'name' => 'Dr. Jane Doe',
            'institution' => 'MIT Institute of Technology',
            'department' => 'Computer Science & AI',
            'bio' => 'Senior researcher in machine learning models.',
        ]);

        Research::create([
            'user_id' => $researcher->id,
            'title' => 'Deep Learning Neural Net Architecture',
            'slug' => 'deep-learning-neural-net-architecture',
            'abstract' => 'Sample abstract for deep learning neural net architecture.',
            'pdf_path' => 'research_pdfs/sample.pdf',
            'views' => 150,
            'downloads' => 45,
            'status' => 'published',
        ]);

        $response = $this->get("/researchers/{$researcher->id}");

        $response->assertStatus(200);
        $response->assertSee('Dr. Jane Doe');
        $response->assertSee('MIT Institute of Technology');
        $response->assertSee('Deep Learning Neural Net Architecture');
        $response->assertSee('150'); // total views
        $response->assertSee('45');  // total downloads
    }
}
