<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileUpdateTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_page_is_displayed(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/user/profile-settings');

        $response->assertStatus(200);
        $response->assertSee('Academic Profile & Account Settings', false);
    }

    public function test_profile_information_can_be_updated(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->put('/user/profile-information', [
            'name' => 'Dr. Rosalind Franklin',
            'email' => 'rfranklin@dna.org',
            'institution' => 'King\'s College London',
            'department' => 'Biophysics',
            'bio' => 'X-ray crystallography specialist.',
        ]);

        $response->assertSessionHasNoErrors();

        $user->refresh();

        $this->assertSame('Dr. Rosalind Franklin', $user->name);
        $this->assertSame('rfranklin@dna.org', $user->email);
        $this->assertSame('King\'s College London', $user->institution);
        $this->assertSame('Biophysics', $user->department);
        $this->assertSame('X-ray crystallography specialist.', $user->bio);
    }
}
