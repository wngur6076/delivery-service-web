<?php

namespace Tests\Feature\CartManagement;

use Mockery;
use Tests\TestCase;
use App\Models\Cart;
use App\Models\Menu;
use App\Models\User;
use App\Models\Eatery;
use App\Models\Option;
use App\Models\MenuGroup;
use App\Models\OptionGroup;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AddCartItemTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function adding_a_valid_item()
    {
        $this->withoutExceptionHandling();

        $user = User::factory()->create();

        $eatery = Mockery::mock(Eatery::class);
        $eatery->shouldReceive('getAttribute')->with('id')->andReturn(1);

        $menuGroup = Mockery::mock(MenuGroup::class);
        $menuGroup->shouldReceive('getAttribute')->with('id')->andReturn(1);

        $menu = Menu::factory()->create([
            'menu_group_id' => $menuGroup->id,
            'name' => '블랙 피넛 커피',
            'price' => 4800,
            'description' => '피넛을 넣은 커피',
            'image_path' => 'menus/test.jpeg',
        ]);

        $optionGroup1 = OptionGroup::factory()->create([
            'eatery_id' => $eatery->id,
            'name' => '맛 선택',
            'required' => true,
            'min' => 1,
            'max' => 1,
        ]);
        $option1 = Option::factory()->create([
            'option_group_id' => $optionGroup1->id,
            'name' => '순한맛',
            'price' => 0,
        ]);
        $option2 = Option::factory()->create([
            'option_group_id' => $optionGroup1->id,
            'name' => '보통맛',
            'price' => 1000,
        ]);
        $option3 = Option::factory()->create([
            'option_group_id' => $optionGroup1->id,
            'name' => '매운맛',
            'price' => 2000,
        ]);

        $optionGroup2 = OptionGroup::factory()->create([
            'eatery_id' => $eatery->id,
            'name' => '추가 선택',
            'required' => false,
            'min' => 0,
            'max' => 2,
        ]);
        $option4 = Option::factory()->create([
            'option_group_id' => $optionGroup2->id,
            'name' => '스콘',
            'price' => 1000,
        ]);

        $response = $this->actingAs($user)->json('POST', '/api/cart', [
            'menu_id' => $menu->id,
            'quantity' => 2,
            'option_ids' => [$option1->id, $option3->id, $option4->id],
        ]);

        $response->assertStatus(200);

        tap(Cart::first(), function ($cart) use ($user) {
            $this->assertTrue($cart->user->is($user));

            tap($cart->items()->first(), function ($item) {
                $menu = $item->menu()->first();
                $this->assertEquals('블랙 피넛 커피', $menu->name);
                $this->assertEquals(4800, $menu->price);
                $this->assertEquals('피넛을 넣은 커피', $menu->description);
                $this->assertEquals('menus/test.jpeg', $menu->image_path);

                $this->assertCount(3, $item->options);
                tap($item->options()->find(1), function ($item_option) {
                    $option = $item_option->option()->first();
                    $this->assertEquals('순한맛', $option->name);
                    $this->assertEquals(0, $option->price);
                });
                tap($item->options()->find(2), function ($item_option) {
                    $option = $item_option->option()->first();
                    $this->assertEquals('매운맛', $option->name);
                    $this->assertEquals(2000, $option->price);
                });
                tap($item->options()->find(3), function ($item_option) {
                    $option = $item_option->option()->first();
                    $this->assertEquals('스콘', $option->name);
                    $this->assertEquals(1000, $option->price);
                });
            });
        });
    }
}
