<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RarityCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_and_view_rarities(): void
    {
        $response = $this->get('/rarities');
        $response->assertStatus(200);

        $response = $this->post('/rarities', [
            'name' => 'Legendary',
            'alias' => 'Leg',
            'key_need_to_summon' => 'legendary-key',
        ]);

        $response->assertRedirect('/rarities');
        $this->assertDatabaseHas('rarities', [
            'name' => 'Legendary',
            'alias' => 'Leg',
        ]);
    }
}
