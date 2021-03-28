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

class AddCartTest extends TestCase
{
    use RefreshDatabase;

    private function validParams($overrides = [])
    {
        $menu = Mockery::mock(Menu::class);
        $menu->shouldReceive('getAttribute')->with('id')->andReturn(1);

        return array_merge([
            'menu_id' => $menu->id,
            'quantity' => 3,
        ], $overrides);
    }

    private function assertValidationError($response, $field)
    {
        $response->assertStatus(422)->assertJsonStructure(['errors' => [$field]]);
    }

    /** @test */
    function guests_cannot_add_new_cart_item()
    {
        $eatery = Eatery::factory()->create();
        $menu = Mockery::mock(Menu::class);
        $menu->shouldReceive('getAttribute')->with('id')->andReturn(1);

        $response = $this->json('POST', '/api/cart', [
            'menu_id' => $menu->id,
            'quantity' => 2,
        ]);

        $response->assertStatus(401);
        $this->assertEquals(0, Cart::count());
    }

    /** @test */
    function adding_a_valid_cart_item()
    {
        $this->withoutExceptionHandling();

        $user = User::factory()->create();

        $eatery = Mockery::mock(Eatery::class);
        $eatery->shouldReceive('getAttribute')->with('id')->andReturn(1);

        $menuGroup = MenuGroup::factory()->create([
            'eatery_id' => $eatery->id,
        ]);

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
            'max' => 2,
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

        $menu->optionGroups()->sync([$optionGroup1->id, $optionGroup2->id]);

        $response = $this->actingAs($user, 'api')->json('POST', '/api/cart', [
            'menu_id' => $menu->id,
            'quantity' => 2,
            'option_ids' => [$option1->id, $option3->id, $option4->id],
        ]);

        $response->assertStatus(200);

        tap(Cart::first(), function ($cart) use ($user) {
            $this->assertTrue($cart->user->is($user));

            tap($cart->items()->first(), function ($item) {
                $this->assertEquals(2, $item->quantity);

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

    /** @test */
    function add_an_existing_cart_item()
    {
        $this->withoutExceptionHandling();

        $user = User::factory()->create();

        $eatery = Mockery::mock(Eatery::class);
        $eatery->shouldReceive('getAttribute')->with('id')->andReturn(1);

        $menuGroup = MenuGroup::factory()->create([
            'eatery_id' => $eatery->id,
        ]);

        $menu1 = Menu::factory()->create([
            'menu_group_id' => $menuGroup->id,
        ]);
        $menu2 = Menu::factory()->create([
            'menu_group_id' => $menuGroup->id,
        ]);

        $optionGroup1 = OptionGroup::factory()->create([
            'eatery_id' => $eatery->id,
            'required' => false,
            'min' => 0,
            'max' => 5,
        ]);
        $option1 = Option::factory()->create([
            'option_group_id' => $optionGroup1->id,
        ]);
        $option2 = Option::factory()->create([
            'option_group_id' => $optionGroup1->id,
        ]);
        $option3 = Option::factory()->create([
            'option_group_id' => $optionGroup1->id,
        ]);

        $optionGroup2 = OptionGroup::factory()->create([
            'eatery_id' => $eatery->id,
            'required' => false,
            'min' => 0,
            'max' => 5,
        ]);
        $option4 = Option::factory()->create([
            'option_group_id' => $optionGroup2->id,
        ]);

        $menu1->optionGroups()->sync([$optionGroup1->id, $optionGroup2->id]);
        $menu2->optionGroups()->sync([$optionGroup1->id]);

        $this->actingAs($user, 'api')->json('POST', '/api/cart', [
            'menu_id' => $menu1->id,
            'quantity' => 2,
            'option_ids' => [$option1->id, $option3->id, $option4->id],
        ]);
        tap(Cart::first(), function ($cart) {
            $this->assertCount(1, $cart->items);
            $this->assertEquals(2, $cart->items->find(1)->quantity);
        });

        $this->actingAs($user->fresh(), 'api')->json('POST', '/api/cart', [
            'menu_id' => $menu1->id,
            'quantity' => 8,
            'option_ids' => [$option1->id, $option3->id, $option4->id],
        ]);
        tap(Cart::first(), function ($cart) {
            $this->assertCount(1, $cart->items);
            $this->assertEquals(10, $cart->items->find(1)->quantity);
        });

        $this->actingAs($user->fresh(), 'api')->json('POST', '/api/cart', [
            'menu_id' => $menu2->id,
            'quantity' => 3,
            'option_ids' => [$option2->id],
        ]);
        tap(Cart::first(), function ($cart) {
            $this->assertCount(2, $cart->items);
            $this->assertEquals(3, $cart->items->find(2)->quantity);
        });
    }

    /** @test */
    function cannot_add_menus_from_other_eatery()
    {
        $user = User::factory()->create();

        $eatery = Eatery::factory()->create();
        $menuGroup = MenuGroup::factory()->create([
            'eatery_id' => $eatery->id,
        ]);
        $menu = Menu::factory()->create([
            'menu_group_id' => $menuGroup->id,
        ]);
        OptionGroup::factory()->create([
            'eatery_id' => $eatery->id,
            'required' => false,
            'min' => 0,
            'max' => 5,
        ]);

        $otherEatery = Eatery::factory()->create();
        $otherMenuGroup = MenuGroup::factory()->create([
            'eatery_id' => $otherEatery->id,
        ]);
        $otherMenu = Menu::factory()->create([
            'menu_group_id' => $otherMenuGroup->id,
        ]);
        OptionGroup::factory()->create([
            'eatery_id' => $otherEatery->id,
            'required' => false,
            'min' => 0,
            'max' => 5,
        ]);

        $this->actingAs($user, 'api')->json('POST', '/api/cart', [
            'menu_id' => $menu->id,
            'quantity' => 3,
        ]);

        $response = $this->actingAs($user->fresh(), 'api')->json('POST', '/api/cart', [
            'menu_id' => $otherMenu->id,
            'quantity' => 3,
        ]);

        $response->assertStatus(422);
        $response->assertJson(['status' => 'eatery_sync_failure']);
        $this->assertEquals($eatery->id, $user->fresh()->cart->eatery_id);
    }

    /** @test */
    function option_group_min_max_validation()
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

        $optionGroup1 = OptionGroup::factory()->create([
            'eatery_id' => $eatery->id,
            'required' => true,
            'min' => 2,
            'max' => 3,
        ]);
        $option1_1 = Option::factory()->create([
            'option_group_id' => $optionGroup1->id,
        ]);
        $option1_2 = Option::factory()->create([
            'option_group_id' => $optionGroup1->id,
        ]);
        $option1_3 = Option::factory()->create([
            'option_group_id' => $optionGroup1->id,
        ]);
        $option1_4 = Option::factory()->create([
            'option_group_id' => $optionGroup1->id,
        ]);

        $optionGroup2 = OptionGroup::factory()->create([
            'eatery_id' => $eatery->id,
            'required' => false,
            'min' => 0,
            'max' => 2,
        ]);
        $option2_1 = Option::factory()->create([
            'option_group_id' => $optionGroup2->id,
        ]);
        $option2_2 = Option::factory()->create([
            'option_group_id' => $optionGroup2->id,
        ]);
        $option2_3 = Option::factory()->create([
            'option_group_id' => $optionGroup2->id,
        ]);

        $menu->optionGroups()->sync([$optionGroup1->id, $optionGroup2->id]);

        $response = $this->actingAs($user, 'api')->json('POST', '/api/cart', [
            'menu_id' => $menu->id,
            'quantity' => 2,
        ]);
        $response->assertStatus(422);
        $response->assertJson(['status' => 'option_count_validation_failure']);

        $response = $this->actingAs($user, 'api')->json('POST', '/api/cart', [
            'menu_id' => $menu->id,
            'quantity' => 2,
            'option_ids' => [$option1_1->id],
        ]);
        $response->assertStatus(422);
        $response->assertJson(['status' => 'option_count_validation_failure']);

        $response = $this->actingAs($user, 'api')->json('POST', '/api/cart', [
            'menu_id' => $menu->id,
            'quantity' => 2,
            'option_ids' => [$option1_1->id, $option1_2->id, $option1_3->id, $option1_4->id],
        ]);
        $response->assertStatus(422);
        $response->assertJson(['status' => 'option_count_validation_failure']);

        $response = $this->actingAs($user, 'api')->json('POST', '/api/cart', [
            'menu_id' => $menu->id,
            'quantity' => 2,
            'option_ids' => [$option1_1->id, $option1_2->id, $option2_1->id, $option2_2->id, $option2_3->id],
        ]);
        $response->assertStatus(422);
        $response->assertJson(['status' => 'option_count_validation_failure']);
    }

    /** @test */
    function menu_id_is_required()
    {
        $user = User::factory()->create();
        $eatery = Eatery::factory()->create();

        $response = $this->actingAs($user, 'api')->json('POST', '/api/cart', $this->validParams([
            'menu_id' => '',
        ]));

        $this->assertValidationError($response, 'menu_id');
    }

    /** @test */
    function quantity_is_required()
    {
        $user = User::factory()->create();
        $eatery = Eatery::factory()->create();

        $response = $this->actingAs($user, 'api')->json('POST', '/api/cart', $this->validParams([
            'quantity' => '',
        ]));

        $this->assertValidationError($response, 'quantity');
    }

    /** @test */
    function quantity_must_be_numeric()
    {
        $user = User::factory()->create();
        $eatery = Eatery::factory()->create();

        $response = $this->actingAs($user, 'api')->json('POST', '/api/cart', $this->validParams([
            'quantity' => 'not a price',
        ]));

        $this->assertValidationError($response, 'quantity');
    }

    /** @test */
    function option_ids_is_optional()
    {
        $user = User::factory()->create();

        $eatery = Mockery::mock(Eatery::class);
        $eatery->shouldReceive('getAttribute')->with('id')->andReturn(1);

        $menuGroup = MenuGroup::factory()->create([
            'eatery_id' => $eatery->id,
        ]);

        $menu = Menu::factory()->create([
            'menu_group_id' => $menuGroup->id,
        ]);

        $optionGroup1 = OptionGroup::factory()->create([
            'eatery_id' => $eatery->id,
            'required' => false,
            'min' => 0,
            'max' => 3,
        ]);
        $option1_1 = Option::factory()->create([
            'option_group_id' => $optionGroup1->id,
        ]);

        $optionGroup2 = OptionGroup::factory()->create([
            'eatery_id' => $eatery->id,
            'required' => false,
            'min' => 0,
            'max' => 2,
        ]);
        $option2_1 = Option::factory()->create([
            'option_group_id' => $optionGroup2->id,
        ]);

        $menu->optionGroups()->sync([$optionGroup1->id, $optionGroup2->id]);

        $response = $this->actingAs($user, 'api')->json('POST', '/api/cart', [
            'menu_id' => $menu->id,
            'quantity' => 2,
            'option_ids' => null,
        ]);

        $response->assertStatus(200);
        $this->assertEquals(2, $user->fresh()->cart->items->first()->quantity);
    }
}
