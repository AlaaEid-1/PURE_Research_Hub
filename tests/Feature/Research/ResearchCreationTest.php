<?php

namespace Tests\Feature\Research;

use App\Enums\DownloadPermission;
use App\Models\Research;
use App\Models\ResearchCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ResearchCreationTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_researcher_can_publish_paper_with_pdf(): void
    {
        Storage::fake('private_research');

        $user = User::factory()->create();
        $category = ResearchCategory::create([
            'name' => 'Computer Science',
            'slug' => 'computer-science',
        ]);

        $pdf = UploadedFile::fake()->create('paper.pdf', 1000, 'application/pdf');
        $f = fopen($pdf->getRealPath(), 'c');
        fwrite($f, '%PDF');
        fclose($f);

        $response = $this->actingAs($user)->post('/dashboard/research', [
            'title' => 'Quantum Neural Networks Optimization',
            'abstract' => 'A comprehensive study on quantum neural networks for optimization problems.',
            'keywords' => 'Quantum, AI, Neural Networks',
            'category_id' => $category->id,
            'download_permission' => DownloadPermission::FREE->value,
            'pdf_file' => $pdf,
        ]);

        $response->assertRedirect('/dashboard/research');
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('researches', [
            'user_id' => $user->id,
            'title' => 'Quantum Neural Networks Optimization',
            'slug' => 'quantum-neural-networks-optimization',
            'category_id' => $category->id,
        ]);

        $research = Research::where('slug', 'quantum-neural-networks-optimization')->first();
        $this->assertNotNull($research);
        Storage::disk('private_research')->assertExists($research->pdf_path);

        $this->assertDatabaseHas('research_authors', [
            'research_id' => $research->id,
            'user_id' => $user->id,
            'author_order' => 1,
        ]);
    }

    public function test_pdf_file_is_required_for_creation(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/dashboard/research', [
            'title' => 'Test Without PDF',
            'abstract' => 'Invalid attempt without pdf document attached.',
            'download_permission' => DownloadPermission::FREE->value,
        ]);

        $response->assertSessionHasErrors('pdf_file');
    }
}
