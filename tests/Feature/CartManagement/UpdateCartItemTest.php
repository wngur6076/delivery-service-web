<?php

namespace Tests\Feature\CartManagement;

use Mockery;
use Tests\TestCase;
use App\Models\Cart;
use App\Models\Menu;
use App\Models\User;
use App\Models\Eatery;
use App\Models\CartItem;
use App\Models\MenuGroup;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UpdateCartItemTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function user_can_update_the_quantity_of_cart_items()
    {
        $this->withoutExceptionHandling();
        $user = User::factory()->create();

        $eatery = Mockery::mock(Eatery::class);
        $eatery->shouldReceive('getAttribute')->with('id')->andReturn(1);
        $menuGroup = Mockery::mock(MenuGroup::class);
        $menuGroup->shouldReceive('getAttribute')->with('id')->andReturn(1);
        $cart = Cart::factory()->create(['eatery_id' => $eatery->id, 'user_id' => $user->id]);
        $menu = Menu::factory()->create([
            'menu_group_id' => $menuGroup->id,
        ]);

        $cartItem = CartItem::factory()->create([
            'cart_id' => $cart->id,
            'menu_id' => $menu->id,
            'quantity' => 2,
        ]);

        $response = $this->actingAs($user, 'api')->json('PATCH', "/api/cart-items/{$cartItem->id}", [
            'quantity' => 10,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'data' => [
                'quantity' => 10,
            ]
        ]);
        tap($cartItem->fresh(), function ($cartItem) {
            $this->assertEquals(10, $cartItem->quantity);
        });
    }
}
