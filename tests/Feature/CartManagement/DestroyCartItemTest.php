<?php

namespace Tests\Feature\CartManagement;

use Mockery;
use Tests\TestCase;
use App\Models\Cart;
use App\Models\Menu;
use App\Models\User;
use App\Models\Eatery;
use App\Models\Option;
use App\Models\CartItem;
use App\Models\MenuGroup;
use App\Models\OptionGroup;
use App\Models\CartItemOption;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DestroyCartItemTest extends TestCase
{
    use RefreshDatabase;

    private function attributes($overrides = [])
    {
        DB::statement(DB::raw('PRAGMA foreign_keys=0'));

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

    /** @test */
    function guest_cannot_delete_cart_items()
    {
        $cartItem = CartItem::factory()->create($this->attributes());

        $response = $this->json('DELETE', "/api/cart-items/{$cartItem->id}");

        $response->assertStatus(401);

        $this->assertDatabaseHas('cart_items', ['id' => $cartItem->id]);
    }

    /** @test */
    public function user_can_delete_their_own_cart_items()
    {
        $this->withoutExceptionHandling();
        $user = User::factory()->create();

        $eatery = Mockery::mock(Eatery::class);
        $eatery->shouldReceive('getAttribute')->with('id')->andReturn(1);

        $menuGroup = Mockery::mock(MenuGroup::class);
        $menuGroup->shouldReceive('getAttribute')->with('id')->andReturn(1);

        $menu = Menu::factory()->create([
            'menu_group_id' => $menuGroup->id,
        ]);

        $optionGroup = Mockery::mock(OptionGroup::class);
        $optionGroup->shouldReceive('getAttribute')->with('id')->andReturn(1);

        $option1 = Option::factory()->create([
            'option_group_id' => $optionGroup->id,
        ]);
        $option2 = Option::factory()->create([
            'option_group_id' => $optionGroup->id,
        ]);
        $option3 = Option::factory()->create([
            'option_group_id' => $optionGroup->id,
        ]);

        $cart = Cart::factory()->create([
            'eatery_id' => $eatery->id,
            'user_id' => $user->id,
        ]);

        $cartItem = CartItem::factory()->create([
            'cart_id' => $cart->id,
            'menu_id' => $menu->id,
            'quantity' => 2,
        ]);

        $cartItemOption1 = CartItemOption::factory()->create([
            'cart_item_id' => $cartItem,
            'option_id' => $option1,
        ]);

        $cartItemOption2 = CartItemOption::factory()->create([
            'cart_item_id' => $cartItem,
            'option_id' => $option2,
        ]);

        $cartItemOption3 = CartItemOption::factory()->create([
            'cart_item_id' => $cartItem,
            'option_id' => $option3,
        ]);

        $response = $this->actingAs($user, 'api')->json('DELETE', "/api/cart-items/{$cartItem->id}");

        $response->assertStatus(204);

        $this->assertDatabaseMissing('cart_items', ['id' => $cartItem->id]);
        $this->assertDatabaseMissing('cart_item_options', ['id' => $cartItemOption1->id]);
        $this->assertDatabaseMissing('cart_item_options', ['id' => $cartItemOption2->id]);
        $this->assertDatabaseMissing('cart_item_options', ['id' => $cartItemOption3->id]);
    }

    /** @test */
    function other_user_cannot_delete_cart_items()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $eatery = Mockery::mock(Eatery::class);
        $eatery->shouldReceive('getAttribute')->with('id')->andReturn(1);

        $menuGroup = Mockery::mock(MenuGroup::class);
        $menuGroup->shouldReceive('getAttribute')->with('id')->andReturn(1);

        $menu = Menu::factory()->create([
            'menu_group_id' => $menuGroup->id,
        ]);

        $optionGroup = Mockery::mock(OptionGroup::class);
        $optionGroup->shouldReceive('getAttribute')->with('id')->andReturn(1);

        $option1 = Option::factory()->create([
            'option_group_id' => $optionGroup->id,
        ]);

        $cart = Cart::factory()->create([
            'eatery_id' => $eatery->id,
            'user_id' => $user->id,
        ]);

        $otherCart = Cart::factory()->create([
            'eatery_id' => $eatery->id,
            'user_id' => $otherUser->id,
        ]);

        $cartItem = CartItem::factory()->create([
            'cart_id' => $cart->id,
            'menu_id' => $menu->id,
            'quantity' => 2,
        ]);

        $cartItemOption1 = CartItemOption::factory()->create([
            'cart_item_id' => $cartItem,
            'option_id' => $option1,
        ]);

        $response = $this->actingAs($otherUser, 'api')->json('DELETE', "/api/cart-items/{$cartItem->id}");
        $response->assertStatus(404);

        $this->assertDatabaseHas('cart_items', ['id' => $cartItem->id]);
        $this->assertDatabaseHas('cart_item_options', ['id' => $cartItemOption1->id]);
    }
}
