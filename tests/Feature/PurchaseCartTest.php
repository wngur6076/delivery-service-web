<?php

namespace Tests\Feature;

use Mockery;
use Tests\TestCase;
use App\Models\Menu;
use App\Models\User;
use App\Models\Eatery;
use App\Models\Option;
use App\Models\MenuGroup;
use App\Models\OptionGroup;
use App\Billing\PaymentGateway;
use App\Billing\FakePaymentGateway;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PurchaseCartTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function customer_can_purchase_menus_to_a_eatery()
    {
        $this->withoutExceptionHandling();
        $user = User::factory()->create([
            'email' => 'john@example.com',
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

        $cart->addItem($menu->id, 2, [$option1->id, $option2->id]);
        $cart->eaterySync($eatery->id);

        $paymentGateway = new FakePaymentGateway;
        $this->app->instance(PaymentGateway::class, $paymentGateway);

        $response = $this->actingAs($user, 'api')->json('POST', "/api/user-cart/{$user->id}/orders", [
            'to_shopkeeper' => ['comment' => '리뷰할게요.', 'disposable_spoon' => true],
            'to_delivery_man' => ['comment' => '안전하게 와주세요.'],
            'payment_token' => $paymentGateway->getValidTestToken(),
        ]);

        $response->assertStatus(201);
        $response->assertJson([
            'data' => [
                'eatery_title' => '만랩커피 강남점',
                'delivery_address' => '서울 강동구 양재대로 96길 79 101동 1001호',
                'comment_to_shopkeeper' => '리뷰할게요.',
                'comment_to_delivery_man' => '안전하게 와주세요.',
                'order_amount' => '12,600',
                'delivery_charge' => '2,000',
                'menus' => [
                    [
                        'cart_item_id' => 1,
                        'name' => '블랙 피넛 커피',
                        'price' => '4,800',
                        'quantity' => 2,
                        'options' => [
                            [
                                'name' => '순한맛',
                                'price' => '0',
                            ],
                            [
                                'name' => '보통맛',
                                'price' => '1,500',
                            ],
                        ]
                    ],
                ],
            ]
        ]);

        $this->assertEquals(12600, $paymentGateway->totalCharges());
    }
}
