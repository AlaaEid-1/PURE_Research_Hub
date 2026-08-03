<?php

namespace Tests\Feature\Research;

use App\Enums\DownloadPermission;
use App\Models\Research;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ResearchAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_cannot_edit_or_update_another_researchers_paper(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();

        $research = Research::create([
            'user_id' => $owner->id,
            'title' => 'Owner Exclusive Paper',
            'slug' => 'owner-exclusive-paper',
            'abstract' => 'Exclusive abstract content for authorization testing.',
            'pdf_path' => 'research_pdfs/sample.pdf',
            'download_permission' => DownloadPermission::FREE,
        ]);

        $response = $this->actingAs($otherUser)->get("/dashboard/research/{$research->id}/edit");
        $response->assertStatus(403);

        $updateResponse = $this->actingAs($otherUser)->put("/dashboard/research/{$research->id}", [
            'title' => 'Hacked Title Attempt',
            'abstract' => 'Hacked abstract attempt content.',
            'download_permission' => DownloadPermission::FREE->value,
        ]);
        $updateResponse->assertStatus(403);

        $deleteResponse = $this->actingAs($otherUser)->delete("/dashboard/research/{$research->id}");
        $deleteResponse->assertStatus(403);
    }

    public function test_admin_can_update_and_delete_any_research_paper(): void
    {
        $owner = User::factory()->create();
        $admin = User::factory()->admin()->create();

        $research = Research::create([
            'user_id' => $owner->id,
            'title' => 'Paper To Be Managed',
            'slug' => 'paper-to-be-managed',
            'abstract' => 'Abstract for admin management test.',
            'pdf_path' => 'research_pdfs/sample.pdf',
            'download_permission' => DownloadPermission::FREE,
        ]);

        $editResponse = $this->actingAs($admin)->get("/dashboard/research/{$research->id}/edit");
        $editResponse->assertStatus(200);

        $deleteResponse = $this->actingAs($admin)->delete("/dashboard/research/{$research->id}");
        $deleteResponse->assertRedirect('/dashboard/research');

        $this->assertDatabaseMissing('researches', ['id' => $research->id]);
    }
}
