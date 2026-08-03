<?php

namespace Tests\Feature\Research;

use App\Enums\DownloadPermission;
use App\Enums\ResearchStatus;
use App\Models\Research;
use App\Models\ResearchCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SearchTest extends TestCase
{
    use RefreshDatabase;

    private function createPublishedResearch(User $user, array $overrides = []): Research
    {
        return Research::create(array_merge([
            'user_id' => $user->id,
            'title' => 'Default Search Test Paper',
            'slug' => 'search-test-'.uniqid(),
            'abstract' => 'Default abstract content for search testing purposes.',
            'keywords' => 'keyword1, keyword2',
            'pdf_path' => 'research_pdfs/test.pdf',
            'download_permission' => DownloadPermission::FREE,
            'status' => ResearchStatus::PUBLISHED,
            'publication_date' => '2024-06-15',
            'views' => 0,
            'downloads' => 0,
        ], $overrides));
    }

    public function test_research_catalog_is_accessible(): void
    {
        $response = $this->get('/research');

        $response->assertStatus(200);
    }

    public function test_search_by_title_keyword_returns_matching_results(): void
    {
        $user = User::factory()->create();

        $this->createPublishedResearch($user, [
            'title' => 'Quantum Computing Applications in Cryptography',
            'slug' => 'quantum-computing-cryptography',
        ]);

        $this->createPublishedResearch($user, [
            'title' => 'Machine Learning for Medical Imaging',
            'slug' => 'machine-learning-medical',
        ]);

        $response = $this->get('/research?query=Quantum+Computing');

        $response->assertStatus(200);
        $response->assertSee('Quantum Computing Applications in Cryptography');
        $response->assertDontSee('Machine Learning for Medical Imaging');
    }

    public function test_search_by_year_filters_correctly(): void
    {
        $user = User::factory()->create();

        $this->createPublishedResearch($user, [
            'title' => 'Research Published in 2024',
            'slug' => 'research-2024',
            'publication_date' => '2024-03-10',
        ]);

        $this->createPublishedResearch($user, [
            'title' => 'Research Published in 2023',
            'slug' => 'research-2023',
            'publication_date' => '2023-06-20',
        ]);

        $response = $this->get('/research?year=2024');

        $response->assertStatus(200);
        $response->assertSee('Research Published in 2024');
        $response->assertDontSee('Research Published in 2023');
    }

    public function test_search_by_category_filters_correctly(): void
    {
        $user = User::factory()->create();
        $category = ResearchCategory::create([
            'name' => 'Artificial Intelligence',
            'slug' => 'artificial-intelligence',
            'description' => 'AI research papers.',
        ]);

        $this->createPublishedResearch($user, [
            'title' => 'AI Research Paper In Category',
            'slug' => 'ai-research-paper',
            'category_id' => $category->id,
        ]);

        $this->createPublishedResearch($user, [
            'title' => 'Physics Research Paper No Category',
            'slug' => 'physics-research-paper',
            'category_id' => null,
        ]);

        $response = $this->get('/research?category=artificial-intelligence');

        $response->assertStatus(200);
        $response->assertSee('AI Research Paper In Category');
        $response->assertDontSee('Physics Research Paper No Category');
    }

    public function test_draft_papers_are_not_visible_in_public_catalog(): void
    {
        $user = User::factory()->create();

        Research::create([
            'user_id' => $user->id,
            'title' => 'This Paper Should Be Hidden From Catalog',
            'slug' => 'hidden-draft-paper',
            'abstract' => 'This draft paper abstract should not appear.',
            'pdf_path' => 'research_pdfs/test.pdf',
            'download_permission' => DownloadPermission::FREE,
            'status' => ResearchStatus::DRAFT,
        ]);

        $response = $this->get('/research');

        $response->assertStatus(200);
        $response->assertDontSee('This Paper Should Be Hidden From Catalog');
    }

    public function test_sort_by_most_viewed_returns_correct_order(): void
    {
        $user = User::factory()->create();

        $this->createPublishedResearch($user, [
            'title' => 'Low Views Paper',
            'slug' => 'low-views',
            'views' => 5,
        ]);

        $this->createPublishedResearch($user, [
            'title' => 'High Views Paper',
            'slug' => 'high-views',
            'views' => 500,
        ]);

        $response = $this->get('/research?sort=most_viewed');

        $response->assertStatus(200);
        $response->assertSeeInOrder(['High Views Paper', 'Low Views Paper']);
    }
}
