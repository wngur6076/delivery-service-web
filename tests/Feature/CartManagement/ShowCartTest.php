<?php

namespace Tests\Feature\CartManagement;

use Mockery;
use Tests\TestCase;
use App\Models\Menu;
use App\Models\User;
use App\Models\Eatery;
use App\Models\Option;
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
        $user = User::factory()->create([
            'address' => '서울 강동구 양재대로 96길 79 101동 1001호',
        ]);
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
            'price' => 1500,
        ]);

        $menu->optionGroups()->sync([$optionGroup->id]);

        $cart->addItem($menu->id, 5, [$option1->id, $option2->id]);
        $cart->eaterySync($eatery->id);

        $response = $this->actingAs($user, 'api')->json('GET', "/api/user-carts/{$user->id}");

        $response->assertStatus(200);
        $response->assertJson([
            'data' => [
                'delivery_address' => '서울 강동구 양재대로 96길 79 101동 1001호',
                'cart' => [
                    'eatery_id' => 1,
                    'eatery_title' => '만랩커피 강남점',
                    'items' => [
                        [
                            'id' => 1,
                            'menu_name' => '블랙 피넛 커피',
                            'menu_price' => '4,800',
                            'quantity' => 5,
                            'options' => [
                                [
                                    'option_name' => '순한맛',
                                    'option_price' => '0',
                                ],
                                [
                                    'option_name' => '보통맛',
                                    'option_price' => '1,500',
                                ],
                            ]
                        ],
                    ]
                ],
                'payment_amount' => [
                    'order_amount' => '31,500',
                    'delivery_charge' => '2,000',
                ],
            ]
        ]);
    }
}
