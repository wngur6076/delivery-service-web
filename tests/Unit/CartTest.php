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

    private function getMockEatery()
    {
        $eatery = Mockery::mock(Eatery::class);
        $eatery->shouldReceive('getAttribute')->with('id')->andReturn(1);

        return $eatery;
    }

    private function getMockMenuGroup()
    {
        $menuGroup = Mockery::mock(MenuGroup::class);
        $menuGroup->shouldReceive('getAttribute')->with('id')->andReturn(1);

        return $menuGroup;
    }

    /** @test */
    function can_add_item()
    {
        $user = User::factory()->create();
        $cart = $user->getCart($this->getMockEatery()->id);

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
        $cart = $user->getCart($this->getMockEatery()->id);

        $menu = Menu::factory()->create(['menu_group_id' => $this->getMockMenuGroup()->id]);
        $optionGroup = Mockery::mock(OptionGroup::class);
        $optionGroup->shouldReceive('getAttribute')->with('id')->andReturn(1);
        $option1 = Option::factory()->create(['option_group_id' => $optionGroup->id]);
        $option2 = Option::factory()->create(['option_group_id' => $optionGroup->id]);
        $option3 = Option::factory()->create(['option_group_id' => $optionGroup->id]);

        // item 추가 대는지 테스트
        $cart->addItem($menu->id, 2);
        $this->assertCount(1, $cart->items);
        $this->assertCount(0, $cart->items()->find(1)->options);
        $this->assertEquals(2, $cart->items()->find(1)->quantity);

        $cart->addItem($menu->id, 3, [$option1->id]);
        $this->assertCount(2, $cart->fresh()->items);
        $this->assertCount(1, $cart->items()->find(2)->options);
        $this->assertEquals(3, $cart->items()->find(2)->quantity);

        $cart->addItem($menu->id, 1, [$option1->id, $option2->id]);
        $this->assertCount(3, $cart->fresh()->items);
        $this->assertCount(2, $cart->items()->find(3)->options);
        $this->assertEquals(1, $cart->items()->find(3)->quantity);

        // 기존 item들에 수량만 추가 하는지 테스트
        $cart->addItem($menu->id, 4);
        $this->assertCount(3, $cart->fresh()->items);
        $this->assertCount(0, $cart->items()->find(1)->options);
        $this->assertEquals(6, $cart->items()->find(1)->quantity);

        $cart->addItem($menu->id, 1, [$option1->id]);
        $this->assertCount(3, $cart->fresh()->items);
        $this->assertCount(1, $cart->items()->find(2)->options);
        $this->assertEquals(4, $cart->items()->find(2)->quantity);

        $cart->addItem($menu->id, 10, [$option2->id, $option1->id]);
        $this->assertCount(3, $cart->fresh()->items);
        $this->assertCount(2, $cart->items()->find(3)->options);
        $this->assertEquals(11, $cart->items()->find(3)->quantity);

        // 다시 item 추가 대는지 테스트
        $cart->addItem($menu->id, 7, [$option1->id, $option3->id, $option2->id]);
        $this->assertCount(4, $cart->fresh()->items);
        $this->assertCount(3, $cart->items()->find(4)->options);
        $this->assertEquals(7, $cart->items()->find(4)->quantity);
    }

    /** @test */
    function can_be_added_items_from_the_same_menu()
    {
        $user = User::factory()->create();
        $cart = $user->getCart($this->getMockEatery()->id);

        $menu1 = Menu::factory()->create(['menu_group_id' => $this->getMockMenuGroup()->id]);
        $menu2 = Menu::factory()->create(['menu_group_id' => $this->getMockMenuGroup()->id]);
        $optionGroup = Mockery::mock(OptionGroup::class);
        $optionGroup->shouldReceive('getAttribute')->with('id')->andReturn(1);
        $option1 = Option::factory()->create(['option_group_id' => $optionGroup->id]);
        $option2 = Option::factory()->create(['option_group_id' => $optionGroup->id]);

        // 메뉴1의 아이템 추가 테스트
        $cart->addItem($menu1->id, 5, [$option1->id, $option2->id]);
        $this->assertCount(1, $cart->items);
        $this->assertCount(2, $cart->items()->find(1)->options);
        $this->assertEquals(5, $cart->items()->find(1)->quantity);

        // 메뉴2의 아이템 추가 테스트
        $cart->addItem($menu2->id, 3, [$option1->id, $option2->id]);
        $this->assertCount(2, $cart->fresh()->items);
        $this->assertCount(2, $cart->items()->find(2)->options);
        $this->assertEquals(3, $cart->items()->find(2)->quantity);

        // 메뉴2의 기존 item에 수량만 추가 하는지 테스트
        $cart->addItem($menu2->id, 8, [$option2->id, $option1->id]);
        $this->assertCount(2, $cart->fresh()->items);
        $this->assertCount(2, $cart->items()->find(2)->options);
        $this->assertEquals(11, $cart->items()->find(2)->quantity);

        // 메뉴2의 다른 옵션을 가지는 아이템이 새로 추가대는지 테스트
        $cart->addItem($menu2->id, 5, [$option2->id]);
        $this->assertCount(3, $cart->fresh()->items);
        $this->assertCount(1, $cart->items()->find(3)->options);
        $this->assertEquals(5, $cart->items()->find(3)->quantity);
    }
}