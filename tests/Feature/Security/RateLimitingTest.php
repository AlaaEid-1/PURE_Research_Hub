<?php

namespace Tests\Feature\Security;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RateLimitingTest extends TestCase
{
    use RefreshDatabase;

    public function test_contact_form_submission_is_rate_limited(): void
    {
        for ($i = 0; $i < 5; $i++) {
            $this->post('/contact', [
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'subject' => 'Inquiry Subject',
                'message' => 'Valid message text body for testing contact form.',
            ])->assertRedirect();
        }

        // 6th request triggers rate limit 429 Too Many Requests
        $response = $this->post('/contact', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'subject' => 'Inquiry Subject',
            'message' => 'Spam message attempt.',
        ]);

        $response->assertStatus(429);
    }
}
