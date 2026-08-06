<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\ResearchCategory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class BrowserVerificationTest extends TestCase
{
    use DatabaseTransactions;
    
    public function test_avatar_browser_flow()
    {
        $user = User::factory()->create();

        $avatar = UploadedFile::fake()->image('avatar.jpg', 600, 600);

        // 1. Upload a profile avatar.
        $this->actingAs($user)->put('/user/profile-information', [
            'name' => 'Test User',
            'email' => $user->email,
            'avatar' => $avatar,
        ])->assertSessionHasNoErrors();
        
        $user->refresh();
        $this->assertNotNull($user->avatar_path);

        // 2 & 3. Refresh the profile page & Inspect generated HTML.
        $response = $this->actingAs($user)->get('/user/profile-settings');
        $html = $response->getContent();
        
        // Output partial HTML to debug if regex fails
        file_put_contents('profile_dump.html', $html);
        
        // Match the exact URL
        if (preg_match('/src="([^"]+avatars[^"]+)"/', $html, $matches)) {
            $url = $matches[1];
            echo "\n[AVATAR] Found URL in HTML: " . $url . "\n";
        } else {
            $this->fail('Avatar URL not found in HTML');
        }
        
        // 4 & 5. Request URL directly against the real dev server!
        $path = parse_url($url, PHP_URL_PATH);
        $fullUrl = 'http://127.0.0.1:8000' . $path;
        
        $context = stream_context_create(['http' => ['ignore_errors' => true]]);
        $content = file_get_contents($fullUrl, false, $context);
        $statusLine = $http_response_header[0];
        preg_match('{HTTP\/\S*\s(\d{3})}', $statusLine, $match);
        $status = $match[1];
        
        // 6. Verify HTTP 200
        echo "[AVATAR] HTTP Status from 127.0.0.1:8000: " . $status . "\n";
        $this->assertEquals(200, $status);
        
        // 7. Verify the image renders (Content-Type)
        $contentType = '';
        foreach ($http_response_header as $header) {
            if (stripos($header, 'Content-Type:') === 0) {
                $contentType = $header;
            }
        }
        echo "[AVATAR] " . $contentType . "\n";
        $this->assertStringContainsString('image', strtolower($contentType));
    }
    
    public function test_research_thumbnail_browser_flow()
    {
        $user = User::factory()->create();
        $category = ResearchCategory::first() ?? ResearchCategory::create(['name' => 'Test', 'slug' => 'test']);

        $thumbnail = UploadedFile::fake()->image('thumbnail.webp', 800, 600);

        // 1. Upload a thumbnail and save research
        $this->actingAs($user)->post('/dashboard/research', [
            'title' => 'Test Research Thumbnail Flow',
            'abstract' => 'This is a sufficiently long abstract to pass the twenty character rule.',
            'category_id' => $category->id,
            'authors' => [['name' => 'John Doe']],
            'publication_date' => '2026-08-01',
            'status' => 'published',
            'download_permission' => 'free',
            'thumbnail_file' => $thumbnail,
            'pdf_file' => UploadedFile::fake()->createWithContent('dummy.pdf', "%PDF-1.4\n%âãÏÓ\n1 0 obj\n<< /Type /Catalog /Pages 2 0 R >>\nendobj\n2 0 obj\n<< /Type /Pages /Kids [3 0 R] /Count 1 >>\nendobj\n3 0 obj\n<< /Type /Page /Parent 2 0 R /Resources << >> /MediaBox [0 0 612 792] >>\nendobj\nxref\n0 4\n0000000000 65535 f\n0000000015 00000 n\n0000000068 00000 n\n0000000127 00000 n\ntrailer\n<< /Size 4 /Root 1 0 R >>\nstartxref\n219\n%%EOF"),
        ])->assertSessionHasNoErrors();
        
        $research = \App\Models\Research::latest()->first();

        // 2 & 3. Open research page & verify thumbnail displayed
        $response = $this->actingAs($user)->get('/research/' . $research->slug);
        $html = $response->getContent();
        
        // In the research show view, the thumbnail is passed to og:image and displayed in cards
        if (preg_match('/property="og:image" content="([^"]+)"/', $html, $matches)) {
            $url = $matches[1];
            echo "\n[THUMBNAIL] Found URL in HTML: " . $url . "\n";
        } else {
            file_put_contents('research_dump.html', $html);
            $this->fail('Thumbnail URL not found in HTML');
        }
        
        // 4. Request URL directly against the real dev server (since it's a static file now)
        $path = parse_url($url, PHP_URL_PATH);
        $fullUrl = 'http://127.0.0.1:8000' . $path;
        
        $context = stream_context_create(['http' => ['ignore_errors' => true]]);
        $content = file_get_contents($fullUrl, false, $context);
        $statusLine = $http_response_header[0];
        preg_match('{HTTP\/\S*\s(\d{3})}', $statusLine, $match);
        $status = $match[1];
        
        echo "[THUMBNAIL] HTTP Status from 127.0.0.1:8000: " . $status . "\n";
        $this->assertEquals(200, $status);
        
        $contentType = '';
        foreach ($http_response_header as $header) {
            if (stripos($header, 'Content-Type:') === 0) {
                $contentType = $header;
            }
        }
        echo "[THUMBNAIL] " . $contentType . "\n";
        $this->assertStringContainsString('image', strtolower($contentType));
    }
}
