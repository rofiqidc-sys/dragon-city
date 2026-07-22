<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ElementCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_and_view_elements(): void
    {
        $response = $this->get('/elements');
        $response->assertStatus(200);

        $response = $this->post('/elements', [
            'name' => 'Fire',
            'alias' => 'Flame',
        ]);

        $response->assertRedirect('/elements');
        $this->assertDatabaseHas('elements', [
            'name' => 'Fire',
            'alias' => 'Flame',
        ]);
    }
}
