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
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UpdateCartItemTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        DB::statement(DB::raw('PRAGMA foreign_keys=0'));
    }

    private function oldAttributes($overrides = [])
    {
        $cart = Mockery::mock(Cart::class);
        $cart->shouldReceive('getAttribute')->with('id')->andReturn(1);
        $menu = Mockery::mock(Menu::class);
        $menu->shouldReceive('getAttribute')->with('id')->andReturn(1);

        return array_merge([
            'cart_id' => $cart->id,
            'menu_id' => $menu->id,
            'quantity' => 2,
        ], $overrides);
    }

    private function assertValidationError($response, $field)
    {
        $response->assertStatus(422)->assertJsonStructure(['errors' => [$field]]);
    }

    /** @test */
    function guest_cannot_update_cart_items()
    {
        $user = User::factory()->create();

        $cartItem = CartItem::factory()->create($this->oldAttributes([
            'quantity' => 2,
        ]));

        $response = $this->json('PATCH', "/api/user-cart/{$user->id}/cart-items/{$cartItem->id}", [
            'quantity' => 10,
        ]);

        $response->assertStatus(401);

        $this->assertEquals(2, $cartItem->fresh()->quantity);
    }

    /** @test */
    function user_can_update_their_own_cart_items()
    {
        $this->withoutExceptionHandling();
        $user = User::factory()->create();

        $eatery = Mockery::mock(Eatery::class);
        $eatery->shouldReceive('getAttribute')->with('id')->andReturn(1);

        $cart = Cart::factory()->create([
            'eatery_id' => $eatery->id,
            'user_id' => $user->id,
        ]);

        $menuGroup = Mockery::mock(MenuGroup::class);
        $menuGroup->shouldReceive('getAttribute')->with('id')->andReturn(1);
        $menu = Menu::factory()->create([
            'menu_group_id' => $menuGroup->id,
        ]);

        $cartItem = CartItem::factory()->create([
            'cart_id' => $cart->id,
            'menu_id' => $menu->id,
            'quantity' => 2,
        ]);

        $response = $this->actingAs($user, 'api')->json('PATCH', "/api/user-cart/{$user->id}/cart-items/{$cartItem->id}", [
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

    /** @test */
    function other_user_cannot_update_cart_items()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $eatery = Mockery::mock(Eatery::class);
        $eatery->shouldReceive('getAttribute')->with('id')->andReturn(1);

        $cart = Cart::factory()->create([
            'eatery_id' => $eatery->id,
            'user_id' => $user->id,
        ]);

        $otherCart = Cart::factory()->create([
            'eatery_id' => $eatery->id,
            'user_id' => $otherUser->id,
        ]);

        $menu = Mockery::mock(Menu::class);
        $menu->shouldReceive('getAttribute')->with('id')->andReturn(1);

        $cartItem = CartItem::factory()->create([
            'cart_id' => $cart->id,
            'menu_id' => $menu->id,
            'quantity' => 2,
        ]);

        $response = $this->actingAs($otherUser, 'api')->json('PATCH', "/api/user-cart/{$user->id}/cart-items/{$cartItem->id}", [
            'quantity' => 10,
        ]);

        $response->assertStatus(401);
        tap($cartItem->fresh(), function ($cartItem) {
            $this->assertEquals(2, $cartItem->quantity);
        });
    }

    /** @test */
    function quantity_is_required()
    {
        $user = User::factory()->create();

        $cartItem = CartItem::factory()->create($this->oldAttributes([
            'quantity' => 2,
        ]));

        $response = $this->actingAs($user, 'api')->json('PATCH', "/api/user-cart/{$user->id}/cart-items/{$cartItem->id}", [
            'quantity' => '',
        ]);

        $this->assertValidationError($response, 'quantity');
    }

    /** @test */
    function quantity_must_be_numeric()
    {
        $user = User::factory()->create();

        $cartItem = CartItem::factory()->create($this->oldAttributes([
            'quantity' => 2,
        ]));

        $response = $this->actingAs($user, 'api')->json('PATCH', "/api/user-cart/{$user->id}/cart-items/{$cartItem->id}", [
            'quantity' => 'not a quantity',
        ]);

        $this->assertValidationError($response, 'quantity');
    }
}
