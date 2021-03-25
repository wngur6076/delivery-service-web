<?php

namespace Tests\Unit;

use Mockery;
use Tests\TestCase;
use App\Models\Menu;
use App\Models\Eatery;
use App\Models\Option;
use App\Models\MenuGroup;
use App\Models\OptionGroup;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CartTest extends TestCase
{
    use RefreshDatabase;

    private function getMockMenuGroup()
    {
        $eatery = Mockery::mock(Eatery::class);
        $eatery->shouldReceive('getAttribute')->with('id')->andReturn(1);

        $menuGroup = Mockery::mock(MenuGroup::class);
        $menuGroup->shouldReceive('getAttribute')->with('id')->andReturn(1);

        return $menuGroup;
    }

    /** @test */
    function can_add_item()
    {
        $user = User::factory()->create();
        $cart = $user->getCart();

        $menu = Menu::factory()->create(['menu_group_id' => $this->getMockMenuGroup()->id]);
        $optionGroup = Mockery::mock(OptionGroup::class);
        $optionGroup->shouldReceive('getAttribute')->with('id')->andReturn(1);
        $option1 = Option::factory()->create(['option_group_id' => $optionGroup->id]);
        $option2 = Option::factory()->create(['option_group_id' => $optionGroup->id]);

        $cart->addItem($menu->id, 5, [$option1->id, $option2->id]);

        $this->assertCount(1, $cart->items);
        $this->assertCount(2, $cart->items()->first()->options);
        $this->assertEquals(5, $cart->items()->first()->quantity);
    }

    /** @test */
    function only_the_quantity_is_updated_if_the_item_already_exists()
    {
        $user = User::factory()->create();
        $cart = $user->getCart();

        $menu = Menu::factory()->create(['menu_group_id' => $this->getMockMenuGroup()->id]);
        $optionGroup = Mockery::mock(OptionGroup::class);
        $optionGroup->shouldReceive('getAttribute')->with('id')->andReturn(1);
        $option1 = Option::factory()->create(['option_group_id' => $optionGroup->id]);
        $option2 = Option::factory()->create(['option_group_id' => $optionGroup->id]);

        $cart->addItem($menu->id, 5, [$option1->id, $option2->id]);

        $this->assertCount(1, $cart->items);
        $this->assertCount(2, $cart->items()->first()->options);
        $this->assertEquals(5, $cart->items()->first()->quantity);

        $cart->addItem($menu->id, 3, [$option1->id, $option2->id]);

        $this->assertCount(1, $cart->fresh()->items);
        $this->assertCount(2, $cart->items()->first()->options);
        $this->assertEquals(8, $cart->items()->first()->quantity);
    }
}
