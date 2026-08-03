<?php

namespace Tests\Feature\Research;

use App\Enums\DownloadPermission;
use App\Enums\ResearchStatus;
use App\Models\Research;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PolicyAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    private function createResearch(User $owner, array $overrides = []): Research
    {
        return Research::create(array_merge([
            'user_id' => $owner->id,
            'title' => 'Authorization Test Research Paper',
            'slug' => 'authorization-test-research-paper-'.uniqid(),
            'abstract' => 'Abstract content for policy authorization testing.',
            'pdf_path' => 'research_pdfs/test.pdf',
            'download_permission' => DownloadPermission::FREE,
            'status' => ResearchStatus::PUBLISHED,
        ], $overrides));
    }

    public function test_owner_can_edit_their_own_research(): void
    {
        $owner = User::factory()->create();
        $research = $this->createResearch($owner);

        $response = $this->actingAs($owner)->get("/dashboard/research/{$research->id}/edit");

        $response->assertStatus(200);
    }

    public function test_non_owner_cannot_access_edit_form(): void
    {
        $owner = User::factory()->create();
        $stranger = User::factory()->create();
        $research = $this->createResearch($owner);

        $response = $this->actingAs($stranger)->get("/dashboard/research/{$research->id}/edit");

        $response->assertStatus(403);
    }

    public function test_admin_can_edit_any_research(): void
    {
        $owner = User::factory()->create();
        $admin = User::factory()->create(['role' => 'admin']);
        $research = $this->createResearch($owner);

        $response = $this->actingAs($admin)->get("/dashboard/research/{$research->id}/edit");

        $response->assertStatus(200);
    }

    public function test_owner_can_delete_their_own_research(): void
    {
        $owner = User::factory()->create();
        $research = $this->createResearch($owner);

        $response = $this->actingAs($owner)->delete("/dashboard/research/{$research->id}");

        $response->assertRedirect();
        $this->assertDatabaseMissing('researches', ['id' => $research->id]);
    }

    public function test_non_owner_cannot_delete_another_users_research(): void
    {
        $owner = User::factory()->create();
        $stranger = User::factory()->create();
        $research = $this->createResearch($owner);

        $response = $this->actingAs($stranger)->delete("/dashboard/research/{$research->id}");

        $response->assertStatus(403);
        $this->assertDatabaseHas('researches', ['id' => $research->id]);
    }

    public function test_unauthenticated_user_cannot_access_dashboard(): void
    {
        $response = $this->get('/dashboard');

        $response->assertRedirect('/login');
    }

    public function test_admin_can_access_admin_moderation_queue(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get('/admin/research');

        $response->assertStatus(200);
    }

    public function test_non_admin_cannot_access_admin_moderation_queue(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        $response = $this->actingAs($user)->get('/admin/research');

        $response->assertStatus(403);
    }
}
