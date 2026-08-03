<?php

namespace Tests\Feature\Pages;

use Tests\TestCase;

class HomePageTest extends TestCase
{
    public function test_home_page_can_be_rendered(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('PURE Impact');
        $response->assertSee('Publish, Discover & Showcase Academic Research', false);
    }
}
