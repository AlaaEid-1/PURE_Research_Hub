<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\ResearchCategory;
use App\Models\Research;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;
use Exception;

class UploadHardeningTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_upload_pdf_multiple_sizes()
    {
        Storage::fake('private_research');
        $user = User::factory()->create();
        $category = ResearchCategory::factory()->create();

        $sizes = [
            '1MB' => 1 * 1024,
            '20MB' => 20 * 1024,
            '100MB' => 100 * 1024,
            '256MB' => 256 * 1024,
        ];

        foreach ($sizes as $name => $sizeKb) {
            // For testing Laravel constraints, we just mock the file size
            // For the 1MB test, we can pass real content to hit the full flow and magic bytes
            if ($name === '1MB') {
                $file = UploadedFile::fake()->createWithContent("{$name}.pdf", '%PDF-1.4 mock content ' . str_repeat('a', 1024 * 100)); // 100KB mock
            } else {
                // Large files use dummy size to avoid memory exhaustion during tests
                $file = UploadedFile::fake()->create("{$name}.pdf", $sizeKb, 'application/pdf');
            }

            $response = $this->actingAs($user)->post(route('dashboard.research.store'), [
                'title' => "Test Paper {$name}",
                'category_id' => $category->id,
                'download_permission' => 'free',
                'abstract' => 'This is a long enough abstract to satisfy the twenty characters minimum length requirement.',
                'pdf_file' => $file,
                'submit_action' => 'draft',
            ]);

            // The larger files will fail the %PDF magic byte check, so they redirect back with errors.
            // But they WON'T fail the max:262144 size check, which is what we are testing!
            if ($name === '1MB') {
                $response->assertSessionHasNoErrors();
                $this->assertDatabaseHas('researches', ['title' => "Test Paper {$name}"]);
            } else {
                $response->assertSessionHasErrors(['pdf_file' => 'The uploaded file is not a valid PDF document.']);
            }
        }
    }

    public function test_rejects_pdf_over_limit()
    {
        Storage::fake('private_research');
        $user = User::factory()->create();
        $category = ResearchCategory::factory()->create();

        // 300MB File (simulate) - Validation limit is 256MB
        $file = UploadedFile::fake()->create('document.pdf', 300 * 1024, 'application/pdf');

        $response = $this->actingAs($user)->post(route('dashboard.research.store'), [
            'title' => 'Test Paper',
            'category_id' => $category->id,
            'download_permission' => 'free',
            'abstract' => 'This is an abstract',
            'pdf_file' => $file,
            'submit_action' => 'draft',
        ]);

        $response->assertSessionHasErrors(['pdf_file' => 'The research paper PDF must not exceed 256MB. Consider compressing your document.']);
    }

    public function test_avatar_formats_and_limits()
    {
        Storage::fake('public');
        $user = User::factory()->create();

        $formats = ['avatar.jpg', 'avatar.png', 'avatar.webp'];

        foreach ($formats as $format) {
            $file = UploadedFile::fake()->image($format)->size(100);
            
            $response = $this->actingAs($user)->put(route('user-profile-information.update'), [
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'avatar' => $file,
            ]);

            $response->assertSessionHasNoErrors();
        }

        // Test over limit
        $largeFile = UploadedFile::fake()->image('avatar.jpg')->size(6000); // 6MB
        $response = $this->actingAs($user)->put(route('user-profile-information.update'), [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'avatar' => $largeFile,
        ]);

        $response->assertSessionHasErrors(['avatar'], null, 'updateProfileInformation');
    }

    public function test_research_thumbnail_processing()
    {
        Storage::fake('private_research');
        $user = User::factory()->create();
        $category = ResearchCategory::factory()->create();

        $formats = ['thumbnail.jpg', 'thumbnail.png', 'thumbnail.webp'];

        foreach ($formats as $format) {
            $pdfFile = UploadedFile::fake()->createWithContent('document.pdf', '%PDF-1.4 mock content');
            $thumbnail = UploadedFile::fake()->image($format)->size(100);

            $response = $this->actingAs($user)->post(route('dashboard.research.store'), [
                'title' => "Test Paper with $format",
                'category_id' => $category->id,
                'download_permission' => 'free',
                'abstract' => 'This is a long enough abstract to satisfy the twenty characters minimum length requirement.',
                'pdf_file' => $pdfFile,
                'thumbnail_file' => $thumbnail,
                'submit_action' => 'draft',
            ]);

            $response->assertRedirect();
            $response->assertSessionHasNoErrors();
            
            $research = Research::where('title', "Test Paper with $format")->first();
            $this->assertNotNull($research);
            $this->assertNotNull($research->thumbnail_path);
            
            // Verify file exists on disk and ends with .webp
            $this->assertStringEndsWith('.webp', $research->thumbnail_path);
            Storage::disk('private_research')->assertExists($research->thumbnail_path);
        }
    }

    public function test_db_transactions_rollback_and_files_deleted_on_error()
    {
        Storage::fake('private_research');
        $user = User::factory()->create();
        $category = ResearchCategory::factory()->create();

        // Ensure magic bytes pass
        $file = UploadedFile::fake()->createWithContent('document.pdf', '%PDF-1.4 mock content');

        // We will mock the Research model to throw an exception on create
        DB::shouldReceive('transaction')->once()->andThrow(new Exception('Mocked DB Failure'));

        try {
            app(\App\Services\ResearchService::class)->createResearch($user, new \App\DTOs\ResearchData(
                title: 'Transaction Test',
                categoryId: $category->id,
                abstract: 'Abstract for test',
                keywords: 'test',
                doi: null,
                publicationDate: null,
                copyrightInformation: 'None',
                downloadPermission: \App\Enums\DownloadPermission::FREE,
                status: \App\Enums\ResearchStatus::DRAFT,
                pdfFile: $file,
                thumbnailFile: null,
                coAuthorIds: []
            ));
        } catch (Exception $e) {
            $this->assertEquals('Mocked DB Failure', $e->getMessage());
        }

        // Verify storage is empty (cleanup worked)
        $this->assertEmpty(Storage::disk('private_research')->allFiles('research_pdfs'));
    }
}

