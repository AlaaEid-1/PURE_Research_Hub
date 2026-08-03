<?php

namespace Tests\Feature\Research;

use App\Models\Research;
use App\Models\ResearchCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SearchAndFilterTest extends TestCase
{
    use RefreshDatabase;

    public function test_keyword_search_filters_research_catalog(): void
    {
        $user = User::factory()->create();

        Research::create([
            'user_id' => $user->id,
            'title' => 'Quantum Computing Paradigm',
            'slug' => 'quantum-computing-paradigm',
            'abstract' => 'Quantum physics and algorithms overview.',
            'keywords' => 'Quantum, Algorithms',
            'pdf_path' => 'research_pdfs/quantum.pdf',
            'status' => 'published',
        ]);

        Research::create([
            'user_id' => $user->id,
            'title' => 'Organic Chemistry Synthesis',
            'slug' => 'organic-chemistry-synthesis',
            'abstract' => 'Chemical reactions in organic molecules.',
            'keywords' => 'Chemistry, Organic',
            'pdf_path' => 'research_pdfs/chemistry.pdf',
            'status' => 'published',
        ]);

        $response = $this->get('/research?query=Quantum');

        $response->assertStatus(200);
        $response->assertSee('Quantum Computing Paradigm');
        $response->assertDontSee('Organic Chemistry Synthesis');
    }

    public function test_category_and_permission_filters(): void
    {
        $user = User::factory()->create();
        $aiCategory = ResearchCategory::create(['name' => 'Artificial Intelligence', 'slug' => 'ai']);

        Research::create([
            'user_id' => $user->id,
            'category_id' => $aiCategory->id,
            'title' => 'AI Open Access Study',
            'slug' => 'ai-open-access-study',
            'abstract' => 'Open access study on AI algorithms.',
            'pdf_path' => 'research_pdfs/ai.pdf',
            'download_permission' => 'free',
            'status' => 'published',
        ]);

        Research::create([
            'user_id' => $user->id,
            'category_id' => $aiCategory->id,
            'title' => 'AI Restricted Study',
            'slug' => 'ai-restricted-study',
            'abstract' => 'Restricted paper on AI security.',
            'pdf_path' => 'research_pdfs/ai_sec.pdf',
            'download_permission' => 'restricted',
            'status' => 'published',
        ]);

        $response = $this->get('/research?category=ai&permission=free');

        $response->assertStatus(200);
        $response->assertSee('AI Open Access Study');
        $response->assertDontSee('AI Restricted Study');
    }
}
