<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
        $response->assertSee('Join PURE Academic Network');
    }

    public function test_new_users_can_register_and_access_dashboard_directly(): void
    {
        $response = $this->post('/register', [
            'name' => 'Dr. Alexander Fleming',
            'email' => 'afleming@penicillin.org',
            'institution' => 'St Mary Hospital',
            'department' => 'Bacteriology',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect('/dashboard');

        $this->assertDatabaseHas('users', [
            'email' => 'afleming@penicillin.org',
            'institution' => 'St Mary Hospital',
            'department' => 'Bacteriology',
        ]);

        $dashboardResponse = $this->get('/dashboard');
        $dashboardResponse->assertStatus(200);
        $dashboardResponse->assertSee('Welcome back, Dr. Alexander Fleming!');
    }
}
