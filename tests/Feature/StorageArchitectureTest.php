<?php

namespace Tests\Feature;

use App\Models\Research;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class StorageArchitectureTest extends TestCase
{
    use RefreshDatabase;

    public function test_pdf_and_thumbnail_upload_stores_actual_object_key_and_not_boolean()
    {
        Storage::fake('private_research');
        Storage::fake('avatars');
        \Illuminate\Support\Facades\Bus::fake();

        $user = User::factory()->create();

        $pdf = UploadedFile::fake()->create('document.pdf', 100, 'application/pdf');
        $thumbnail = UploadedFile::fake()->image('thumb.jpg', 100, 100);

        // Uploading requires category etc. To test service directly:
        $dto = \App\DTOs\ResearchData::fromArray([
            'title' => 'Test',
            'abstract' => 'Test',
            'category_id' => \App\Models\ResearchCategory::factory()->create()->id,
            'pdf_file' => $pdf,
            'thumbnail_file' => $thumbnail,
            'status' => \App\Enums\ResearchStatus::PUBLISHED->value,
            'download_permission' => 'free',
        ]);

        $service = app(\App\Services\ResearchService::class);
        $research = $service->createResearch($user, $dto);

        $this->assertNotEmpty($research->pdf_path);
        $this->assertNotEquals('0', $research->pdf_path);
        $this->assertNotEquals('1', $research->pdf_path);
        
        $this->assertNotEmpty($research->thumbnail_path);
        $this->assertNotEquals('0', $research->thumbnail_path);
        $this->assertNotEquals('1', $research->thumbnail_path);
    }

    public function test_failed_upload_throws_exception_and_does_not_save_zero()
    {
        $user = User::factory()->create();
        
        // Let's create an uploaded file mock that returns false for storeAs
        $pdf = \Mockery::mock(UploadedFile::class);
        $pdf->shouldReceive('getClientOriginalName')->andReturn('document.pdf');
        $pdf->shouldReceive('getSize')->andReturn(100);
        $pdf->shouldReceive('getMimeType')->andReturn('application/pdf');
        // storeAs returns false
        $pdf->shouldReceive('storeAs')->andReturn(false);

        $dto = \App\DTOs\ResearchData::fromArray([
            'title' => 'Test Fail',
            'abstract' => 'Test',
            'category_id' => \App\Models\ResearchCategory::factory()->create()->id,
            'pdf_file' => $pdf,
            'status' => \App\Enums\ResearchStatus::PUBLISHED->value,
            'download_permission' => 'free',
        ]);

        $service = app(\App\Services\ResearchService::class);
        
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Failed to upload PDF file to storage.');

        $service->createResearch($user, $dto);

        $this->assertDatabaseMissing('researches', ['title' => 'Test Fail']);
    }

    public function test_authorized_download_works()
    {
        Storage::fake('private_research');
        $user = User::factory()->create();
        $category = \App\Models\ResearchCategory::factory()->create();
        $research = Research::forceCreate([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'title' => 'test pdf',
            'abstract' => 'test',
            'pdf_path' => 'research_pdfs/test.pdf',
            'status' => \App\Enums\ResearchStatus::PUBLISHED->value,
            'download_permission' => 'free',
        ]);
        Storage::disk('private_research')->put('research_pdfs/test.pdf', 'dummy content');

        $response = $this->actingAs($user)->get(route('research.download', $research));
        
        $response->assertStatus(200);
        $response->assertHeader('Cache-Control', 'max-age=0, private');
    }

    public function test_missing_pdf_is_handled_gracefully()
    {
        Storage::fake('private_research');
        $user = User::factory()->create();
        $category = \App\Models\ResearchCategory::factory()->create();
        $research = Research::forceCreate([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'title' => 'missing pdf',
            'abstract' => 'test',
            'pdf_path' => 'research_pdfs/missing.pdf',
            'status' => \App\Enums\ResearchStatus::PUBLISHED->value,
            'download_permission' => 'free',
        ]);
        
        $response = $this->actingAs($user)->get(route('research.download', $research));
        
        $response->assertRedirect(route('research.show', $research->slug));
        $response->assertSessionHas('error', 'The requested PDF file is no longer available on the server.');
    }

    public function test_storage_connectivity_error_is_handled_gracefully()
    {
        $user = User::factory()->create();
        $category = \App\Models\ResearchCategory::factory()->create();
        $research = Research::forceCreate([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'title' => 'missing pdf',
            'abstract' => 'test',
            'pdf_path' => 'research_pdfs/test.pdf',
            'status' => \App\Enums\ResearchStatus::PUBLISHED->value,
            'download_permission' => 'free',
        ]);
        
        // Mock the disk to throw exception on exists()
        $disk = \Mockery::mock(\Illuminate\Contracts\Filesystem\Filesystem::class);
        $disk->shouldReceive('exists')->andThrow(new \Exception('S3 SSL Error'));
        Storage::shouldReceive('disk')->with('private_research')->andReturn($disk);

        $response = $this->actingAs($user)->get(route('research.download', $research));
        
        $response->assertRedirect(route('research.show', $research->slug));
        $response->assertSessionHas('error', 'Storage connectivity error. Please try again later.');
    }
}
