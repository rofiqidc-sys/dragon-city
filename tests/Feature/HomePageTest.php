<?php

namespace Tests\Feature;

use Tests\TestCase;

class HomePageTest extends TestCase
{
    public function test_home_page_displays_theme_template(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Flash Able - Most Trusted Admin Template');
        $response->assertSee('Dashboard');
    }
}
