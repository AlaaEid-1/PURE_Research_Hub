<?php

namespace Tests\Feature\Research;

use App\Models\Research;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookmarkTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_toggle_bookmarking_paper(): void
    {
        $user = User::factory()->create();

        $research = Research::create([
            'user_id' => $user->id,
            'title' => 'Bookmarked Quantum Research',
            'slug' => 'bookmarked-quantum-research',
            'abstract' => 'Sample abstract for bookmarked quantum paper.',
            'pdf_path' => 'research_pdfs/sample.pdf',
            'status' => 'published',
        ]);

        // Toggle ON
        $response = $this->actingAs($user)->post("/research/{$research->id}/bookmark");
        $response->assertRedirect();

        $this->assertDatabaseHas('saved_researches', [
            'user_id' => $user->id,
            'research_id' => $research->id,
        ]);

        // Saved collection view
        $collectionResponse = $this->actingAs($user)->get('/dashboard/bookmarks');
        $collectionResponse->assertStatus(200);
        $collectionResponse->assertSee('Bookmarked Quantum Research');

        // Toggle OFF
        $untoggleResponse = $this->actingAs($user)->post("/research/{$research->id}/bookmark");
        $untoggleResponse->assertRedirect();

        $this->assertDatabaseMissing('saved_researches', [
            'user_id' => $user->id,
            'research_id' => $research->id,
        ]);
    }
}
