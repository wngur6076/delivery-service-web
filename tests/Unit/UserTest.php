<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Cart;
use App\Models\User;
use App\Models\Eatery;
use App\Exceptions\EaterySyncException;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function can_add_cart()
    {
        $user = User::factory()->create();
        $this->assertNull($user->cart);

        $user->getCart(Eatery::factory()->create()->id);
        $this->assertNotNull($user->fresh()->cart);
    }

    /** @test */
    function can_bring_a_cart_if_there_is_a_cart()
    {
        $user = User::factory()->create();
        $eatery = Eatery::factory()->create();
        $cart = Cart::create(['user_id' => $user->id, 'eatery_id' => $eatery->id]);
        $this->assertEquals(1, $cart->id);

        $this->assertEquals($cart->id, $user->getCart($eatery->id)->id);
    }

    /** @test */
    function exception_is_thrown_when_adding_another_eatery_menu_to_the_cart()
    {
        $user = User::factory()->create();
        $eatery1 = Eatery::factory()->create();
        $eatery2 = Eatery::factory()->create();

        $user->getCart($eatery1->id);
        try {
            $user->fresh()->getCart($eatery2->id);
        } catch (EaterySyncException $e) {
            $this->assertEquals(1, $user->fresh()->cart->eatery_id);
            return;
        }

        $this->fail("I added another eatery menu and it success.");
    }
}
