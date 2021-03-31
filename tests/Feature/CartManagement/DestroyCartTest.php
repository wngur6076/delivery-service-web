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
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DestroyCartTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function guest_cannot_delete_cart()
    {
        $eatery = Mockery::mock(Eatery::class);
        $eatery->shouldReceive('getAttribute')->with('id')->andReturn(1);

        $user = User::factory()->create();
        $cart = Cart::factory()->create([
            'eatery_id' => $eatery->id,
            'user_id' => $user->id,
        ]);

        $response = $this->json('DELETE', "/api/user-carts/{$user->id}");

        $response->assertStatus(401);

        $this->assertDatabaseHas('carts', ['id' => $cart->id]);
    }

    /** @test */
    public function user_can_delete_cart()
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

        $cartItem1 = CartItem::factory()->create([
            'cart_id' => $cart->id,
            'menu_id' => $menu->id,
            'quantity' => 2,
        ]);

        $cartItemOption1 = CartItemOption::factory()->create([
            'cart_item_id' => $cartItem1,
            'option_id' => $option1,
        ]);

        $cartItemOption2 = CartItemOption::factory()->create([
            'cart_item_id' => $cartItem1,
            'option_id' => $option2,
        ]);

        $cartItemOption3 = CartItemOption::factory()->create([
            'cart_item_id' => $cartItem1,
            'option_id' => $option3,
        ]);

        $cartItem2 = CartItem::factory()->create([
            'cart_id' => $cart->id,
            'menu_id' => $menu->id,
            'quantity' => 5,
        ]);

        $response = $this->actingAs($user, 'api')->json('DELETE', "/api/user-carts/{$user->id}");

        $response->assertStatus(204);

        $this->assertDatabaseMissing('carts', ['id' => $cart->id]);
        $this->assertDatabaseMissing('cart_items', ['id' => $cartItem1->id]);
        $this->assertDatabaseMissing('cart_items', ['id' => $cartItem2->id]);
        $this->assertDatabaseMissing('cart_item_options', ['id' => $cartItemOption1->id]);
        $this->assertDatabaseMissing('cart_item_options', ['id' => $cartItemOption2->id]);
        $this->assertDatabaseMissing('cart_item_options', ['id' => $cartItemOption3->id]);
    }

    /** @test */
    function other_user_cannot_delete_cart()
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

        $response = $this->actingAs($otherUser, 'api')->json('DELETE', "/api/user-carts/{$user->id}");
        $response->assertStatus(401);

        $this->assertDatabaseHas('carts', ['id' => $cart->id]);
        $this->assertDatabaseHas('cart_items', ['id' => $cartItem->id]);
        $this->assertDatabaseHas('cart_item_options', ['id' => $cartItemOption1->id]);
    }
}
