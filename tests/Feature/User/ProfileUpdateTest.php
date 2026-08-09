<?php

namespace Tests\Feature\User;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileUpdateTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_update_profile_with_academic_links_and_research_interests(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->put('/user/profile-information', [
            'name' => 'Dr. Jane Smith',
            'email' => $user->email,
            'institution' => 'MIT Research Lab',
            'department' => 'Artificial Intelligence',
            'bio' => 'Senior AI Researcher focused on neural networks.',
            'research_interests' => 'Machine Learning, Computer Vision, Robotics',
            'orcid_id' => '0000-0002-1825-0097',
            'google_scholar_url' => 'https://scholar.google.com/citations?user=sample123',
            'website_url' => 'https://janesmith-ai.mit.edu',
        ]);

        $response->assertSessionHasNoErrors();

        $user->refresh();

        $this->assertSame('Dr. Jane Smith', $user->name);
        $this->assertSame('MIT Research Lab', $user->institution);
        $this->assertSame('Machine Learning, Computer Vision, Robotics', $user->research_interests);
        $this->assertSame('0000-0002-1825-0097', $user->orcid_id);
        $this->assertSame('https://scholar.google.com/citations?user=sample123', $user->google_scholar_url);
        $this->assertSame('https://janesmith-ai.mit.edu', $user->website_url);
    }

    public function test_invalid_orcid_format_is_rejected(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->put('/user/profile-information', [
            'name' => $user->name,
            'email' => $user->email,
            'orcid_id' => 'not-a-valid-orcid',
        ]);

        // Should have validation error on orcid_id
        $response->assertSessionHasErrorsIn('updateProfileInformation', ['orcid_id']);
    }

    public function test_invalid_google_scholar_url_is_rejected(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->put('/user/profile-information', [
            'name' => $user->name,
            'email' => $user->email,
            'google_scholar_url' => 'https://google.com/not-scholar',
        ]);

        $response->assertSessionHasErrorsIn('updateProfileInformation', ['google_scholar_url']);
    }

    public function test_http_website_url_is_rejected_https_required(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->put('/user/profile-information', [
            'name' => $user->name,
            'email' => $user->email,
            'website_url' => 'http://insecure-website.com',
        ]);

        $response->assertSessionHasErrorsIn('updateProfileInformation', ['website_url']);
    }

    public function test_profile_fields_are_optional_and_can_be_cleared(): void
    {
        $user = User::factory()->create([
            'institution' => 'Old University',
            'orcid_id' => '0000-0002-1825-0097',
        ]);

        $response = $this->actingAs($user)->put('/user/profile-information', [
            'name' => $user->name,
            'email' => $user->email,
            'institution' => '',
            'orcid_id' => '',
        ]);

        $response->assertSessionHasNoErrors();

        $user->refresh();
        $this->assertNull($user->institution);
        $this->assertNull($user->orcid_id);
    }

    public function test_valid_avatar_can_be_uploaded(): void
    {
        \Illuminate\Support\Facades\Storage::fake('avatars');
        $user = User::factory()->create();

        $file = \Illuminate\Http\UploadedFile::fake()->image('avatar.jpg');

        $response = $this->actingAs($user)->put('/user/profile-information', [
            'name' => $user->name,
            'email' => $user->email,
            'avatar' => $file,
        ]);
        
        $response->assertSessionHasNoErrors();
        $user->refresh();
        $this->assertNotNull($user->avatar_path);
        \Illuminate\Support\Facades\Storage::disk('avatars')->assertExists($user->avatar_path);
    }

    public function test_oversized_avatar_is_rejected(): void
    {
        \Illuminate\Support\Facades\Storage::fake('avatars');
        $user = User::factory()->create();

        $file = \Illuminate\Http\UploadedFile::fake()->image('avatar.jpg')->size(6000);

        $response = $this->actingAs($user)->put('/user/profile-information', [
            'name' => $user->name,
            'email' => $user->email,
            'avatar' => $file,
        ]);

        $response->assertSessionHasErrorsIn('updateProfileInformation', ['avatar']);
    }

    public function test_invalid_avatar_file_is_rejected(): void
    {
        \Illuminate\Support\Facades\Storage::fake('avatars');
        $user = User::factory()->create();

        $file = \Illuminate\Http\UploadedFile::fake()->create('avatar.pdf', 100, 'application/pdf');

        $response = $this->actingAs($user)->put('/user/profile-information', [
            'name' => $user->name,
            'email' => $user->email,
            'avatar' => $file,
        ]);

        $response->assertSessionHasErrorsIn('updateProfileInformation', ['avatar']);
    }

    public function test_old_avatar_is_deleted_when_replaced(): void
    {
        \Illuminate\Support\Facades\Storage::fake('avatars');
        $user = User::factory()->create([
            'avatar_path' => 'avatars/old.webp'
        ]);
        \Illuminate\Support\Facades\Storage::disk('avatars')->put('avatars/old.webp', 'old content');

        $file = \Illuminate\Http\UploadedFile::fake()->image('avatar.jpg');

        $response = $this->actingAs($user)->put('/user/profile-information', [
            'name' => $user->name,
            'email' => $user->email,
            'avatar' => $file,
        ]);

        $response->assertSessionHasNoErrors();
        \Illuminate\Support\Facades\Storage::disk('avatars')->assertMissing('avatars/old.webp');
    }

    public function test_avatar_fallback_is_used_when_no_avatar(): void
    {
        $user = User::factory()->create(['avatar_path' => null]);
        $this->assertEquals(asset('images/avatar-fallback.svg'), $user->avatar_url);
    }
}
