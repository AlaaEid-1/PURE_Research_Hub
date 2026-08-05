<?php

namespace Tests\Feature\Security;

use App\Models\Research;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class FileUploadSecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_valid_pdf_manuscript_up_to_256mb_is_accepted_and_stored_privately(): void
    {
        Storage::fake('private_research');
        Storage::fake('public');

        $user = User::factory()->create();

        // 250MB Fake PDF file
        $pdfFile = UploadedFile::fake()->create('heavy_manuscript.pdf', 250000, 'application/pdf');
        $f = fopen($pdfFile->getRealPath(), 'c');
        fwrite($f, '%PDF');
        fclose($f);

        $response = $this->actingAs($user)->post('/dashboard/research', [
            'title' => 'Heavy Quantum Physics Research Paper',
            'abstract' => 'Detailed abstract covering 250MB PDF data set and scientific evidence.',
            'download_permission' => 'free',
            'pdf_file' => $pdfFile,
            'submit_action' => 'submit',
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('researches', [
            'user_id' => $user->id,
            'title' => 'Heavy Quantum Physics Research Paper',
        ]);

        // Assert file exists on private disk, not public web root
        $research = Research::where('title', 'Heavy Quantum Physics Research Paper')->first();
        Storage::disk('private_research')->assertExists($research->pdf_path);
    }

    public function test_non_pdf_file_is_rejected_by_mime_and_extension_validation(): void
    {
        Storage::fake('private_research');

        $user = User::factory()->create();

        // Fake executable or document masquerading as paper
        $exeFile = UploadedFile::fake()->create('malicious_script.exe', 1000, 'application/x-msdownload');
        $docxFile = UploadedFile::fake()->create('paper.docx', 2000, 'application/vnd.openxmlformats-officedocument.wordprocessingml.document');

        $responseExe = $this->actingAs($user)->post('/dashboard/research', [
            'title' => 'Malicious Paper Upload Attempt',
            'abstract' => 'Abstract text for security test.',
            'download_permission' => 'free',
            'pdf_file' => $exeFile,
        ]);
        $responseExe->assertSessionHasErrors('pdf_file');

        $responseDocx = $this->actingAs($user)->post('/dashboard/research', [
            'title' => 'Word Doc Paper Upload Attempt',
            'abstract' => 'Abstract text for security test.',
            'download_permission' => 'free',
            'pdf_file' => $docxFile,
        ]);
        $responseDocx->assertSessionHasErrors('pdf_file');
    }

    public function test_oversized_pdf_greater_than_256mb_is_rejected(): void
    {
        Storage::fake('private_research');

        $user = User::factory()->create();

        // 300MB Fake PDF file (Exceeding 256MB limit of 262144 KB)
        $oversizedPdf = UploadedFile::fake()->create('gigantic_paper.pdf', 307200, 'application/pdf');
        $f = fopen($oversizedPdf->getRealPath(), 'c');
        fwrite($f, '%PDF');
        fclose($f);

        $response = $this->actingAs($user)->post('/dashboard/research', [
            'title' => 'Oversized Paper Test',
            'abstract' => 'Abstract text for oversized upload test.',
            'download_permission' => 'free',
            'pdf_file' => $oversizedPdf,
        ]);

        $response->assertSessionHasErrors('pdf_file');
    }

    public function test_valid_thumbnail_up_to_5mb_is_accepted_and_oversized_rejected(): void
    {
        Storage::fake('private_research');
        Storage::fake('public');

        $user = User::factory()->create();

        $validPdf = UploadedFile::fake()->create('valid.pdf', 1000, 'application/pdf');
        $f = fopen($validPdf->getRealPath(), 'c');
        fwrite($f, '%PDF');
        fclose($f);
        $validImage = UploadedFile::fake()->create('cover.jpg', 4500, 'image/jpeg'); // 4.5MB
        $oversizedImage = UploadedFile::fake()->create('big_cover.png', 5500, 'image/png'); // 5.5MB

        // Valid 4.5MB image
        $responseValid = $this->actingAs($user)->post('/dashboard/research', [
            'title' => 'Valid Image Cover Paper',
            'abstract' => 'Abstract text for valid image cover test.',
            'download_permission' => 'free',
            'pdf_file' => $validPdf,
            'thumbnail_file' => $validImage,
        ]);
        $responseValid->assertRedirect();

        // Oversized 5.5MB image
        $responseOversized = $this->actingAs($user)->post('/dashboard/research', [
            'title' => 'Oversized Image Cover Paper',
            'abstract' => 'Abstract text for oversized image cover test.',
            'download_permission' => 'free',
            'pdf_file' => $validPdf,
            'thumbnail_file' => $oversizedImage,
        ]);
        $responseOversized->assertSessionHasErrors('thumbnail_file');
    }

    public function test_php_upload_configuration_supports_large_files(): void
    {
        if (!env('IS_DOCKER') && env('APP_ENV') === 'testing') {
            $this->markTestSkipped('Local test environment does not use production docker php.ini');
        }
        // Verify that the php.ini settings support 256MB uploads
        // These must be at least 256MB (262144KB) to allow academic research paper uploads
        $uploadMaxFilesize = (int) ini_get('upload_max_filesize');
        $postMaxSize = (int) ini_get('post_max_size');

        // Convert shorthand (e.g. "128M") to bytes for comparison
        $uploadBytes = $this->convertPhpIniSize(ini_get('upload_max_filesize'));
        $postBytes = $this->convertPhpIniSize(ini_get('post_max_size'));

        $this->assertGreaterThanOrEqual(
            256 * 1024 * 1024, // 256MB in bytes
            $uploadBytes,
            'upload_max_filesize must be at least 256MB to support academic PDF uploads'
        );

        $this->assertGreaterThanOrEqual(
            256 * 1024 * 1024,
            $postBytes,
            'post_max_size must be at least 256MB to support academic PDF uploads'
        );
    }

    public function test_security_headers_are_present_on_web_responses(): void
    {
        $response = $this->get('/');

        $response->assertHeader('X-Frame-Options', 'SAMEORIGIN');
        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $response->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->assertHeader('Permissions-Policy', 'camera=(), microphone=(), geolocation=(), payment=()');
        $response->assertHeaderMissing('X-Powered-By');
    }

    /**
     * Convert PHP shorthand memory notation (e.g., "128M") to bytes.
     */
    private function convertPhpIniSize(string $value): int
    {
        $value = trim($value);
        $suffix = strtolower(substr($value, -1));
        $num = (int) $value;

        return match ($suffix) {
            'g' => $num * 1024 * 1024 * 1024,
            'm' => $num * 1024 * 1024,
            'k' => $num * 1024,
            default => $num,
        };
    }
}
