<?php

namespace Tests\Unit;

use App\Models\Cart;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function can_add_cart()
    {
        $user = User::factory()->create();
        $this->assertNull($user->cart);

        $user->getCart();
        $this->assertNotNull($user->fresh()->cart);
    }

    /** @test */
    function can_bring_a_cart_if_there_is_a_cart()
    {
        $user = User::factory()->create();
        $cart = Cart::create(['user_id' => $user->id]);
        $this->assertEquals(1, $cart->id);

        $this->assertEquals($cart->id, $user->getCart()->id);
    }
}
