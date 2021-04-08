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

class ShowCartBannerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function guest_cannot_see_the_cart_banner()
    {
        $user = User::factory()->create();

        $response = $this->json('GET', "/api/user-cart/{$user->id}/banner");

        $response->assertStatus(401);
    }

    /** @test */
    function user_can_see_the_cart_banner()
    {
        $this->withoutExceptionHandling();
        $user = User::factory()->create();
        $eatery = Mockery::mock(Eatery::class);
        $eatery->shouldReceive('getAttribute')->with('id')->andReturn(1);
        $menuGroup = Mockery::mock(MenuGroup::class);
        $menuGroup->shouldReceive('getAttribute')->with('id')->andReturn(1);
        $menu1 = Menu::factory()->create([
            'menu_group_id' => $menuGroup->id,
            'price' => 19000,
        ]);
        $menu2 = Menu::factory()->create([
            'menu_group_id' => $menuGroup->id,
            'price' => 9500,
        ]);

        $optionGroup = OptionGroup::factory()->create([
            'eatery_id' => $eatery->id,
            'required' => false,
            'min' => 0,
            'max' => 5,
        ]);
        $option1 = Option::factory()->create([
            'option_group_id' => $optionGroup->id,
            'price' => 1500,
        ]);
        $option2 = Option::factory()->create([
            'option_group_id' => $optionGroup->id,
            'price' => 4200,
        ]);
        $option3 = Option::factory()->create([
            'option_group_id' => $optionGroup->id,
            'price' => 6350,
        ]);
        $menu1->optionGroups()->sync([$optionGroup->id]);
        $menu2->optionGroups()->sync([$optionGroup->id]);

        $cart = $user->getCart($eatery->id);
        $cart->addItem($menu1->id, 1);
        $cart->addItem($menu1->id, 2, [$option1->id, $option2->id]);
        $cart->addItem($menu2->id, 3, [$option1->id, $option2->id, $option3->id]);

        $response = $this->actingAs($user->fresh(), 'api')->json('GET', "/api/user-cart/{$user->id}/banner");
        $response->assertStatus(200);
        $response->assertJson([
            'data' => [
                'cart_item_count' => 3,
                'cart_item_total' => '133,050',
            ]
        ]);
    }
}
