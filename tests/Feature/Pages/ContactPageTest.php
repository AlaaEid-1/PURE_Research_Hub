<?php

namespace Tests\Feature\Pages;

use Tests\TestCase;

class ContactPageTest extends TestCase
{
    public function test_contact_page_can_be_rendered(): void
    {
        $response = $this->get('/contact');

        $response->assertStatus(200);
        $response->assertSee('Contact Academic Support');
    }

    public function test_contact_form_can_be_submitted(): void
    {
        $response = $this->post('/contact', [
            'name' => 'Dr. Marie Curie',
            'email' => 'mcurie@radium.org',
            'subject' => 'Research Inquiry',
            'message' => 'Inquiry regarding open access paper indexing on PURE Research Hub.',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
    }
}
