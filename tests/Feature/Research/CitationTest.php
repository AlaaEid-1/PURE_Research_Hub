<?php

namespace Tests\Feature\Research;

use App\Models\Research;
use App\Models\ResearchCitation;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CitationTest extends TestCase
{
    use RefreshDatabase;

    public function test_research_citations_can_be_created_and_unique_constraint_enforced(): void
    {
        $user = User::factory()->create();

        $paperA = Research::create([
            'user_id' => $user->id,
            'title' => 'Cited Original Paper A',
            'slug' => 'cited-original-paper-a',
            'abstract' => 'Abstract for Paper A.',
            'pdf_path' => 'research_pdfs/a.pdf',
            'status' => 'published',
        ]);

        $paperB = Research::create([
            'user_id' => $user->id,
            'title' => 'Citing Paper B',
            'slug' => 'citing-paper-b',
            'abstract' => 'Abstract for Paper B.',
            'pdf_path' => 'research_pdfs/b.pdf',
            'status' => 'published',
        ]);

        ResearchCitation::create([
            'research_id' => $paperA->id,
            'cited_by_research_id' => $paperB->id,
        ]);

        $this->assertDatabaseHas('research_citations', [
            'research_id' => $paperA->id,
            'cited_by_research_id' => $paperB->id,
        ]);

        $this->assertSame(1, $paperA->citations()->count());

        // Duplicate citation throws database constraint exception
        $this->expectException(QueryException::class);
        ResearchCitation::create([
            'research_id' => $paperA->id,
            'cited_by_research_id' => $paperB->id,
        ]);
    }
}
