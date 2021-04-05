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
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ShowCartTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function user_can_see_the_cart()
    {
        $this->withoutExceptionHandling();
        $user = User::factory()->create();
        $cart = $user->getCart();


        $eatery = Eatery::factory()->create([
            'title' => '만랩커피 강남점',
            'delivery_charge' => 2000,
            'minimum_order_amount' => 12000,
        ]);

        $menuGroup = Mockery::mock(MenuGroup::class);
        $menuGroup->shouldReceive('getAttribute')->with('id')->andReturn(1);

        $menu = Menu::factory()->create([
            'menu_group_id' => $menuGroup->id,
            'name' => '블랙 피넛 커피',
            'price' => 4800,
        ]);

        $optionGroup = OptionGroup::factory()->create([
            'eatery_id' => $eatery->id,
            'name' => '맛 선택',
            'required' => false,
            'min' => 0,
            'max' => 2,
        ]);
        $option1 = Option::factory()->create([
            'option_group_id' => $optionGroup->id,
            'name' => '순한맛',
            'price' => 0,
        ]);
        $option2 = Option::factory()->create([
            'option_group_id' => $optionGroup->id,
            'name' => '보통맛',
            'price' => 1000,
        ]);

        $menu->optionGroups()->sync([$optionGroup->id]);

        $cart->addItem($menu->id, 5, [$option1->id, $option2->id]);
        $cart->eaterySync($eatery->id);

        dd($cart->with('items')->get()->toArray());
    }
}
