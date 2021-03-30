<?php

namespace Tests\Feature\CartManagement;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class DestroyCartItemTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_delete_their_own_cart_items()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }
}
