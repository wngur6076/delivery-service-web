<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Menu;
use App\Models\Eatery;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MenuTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function can_get_image_url()
    {
        Storage::fake('public');

        $menu = Menu::factory()->make([
            'image_path' => 'menus/test.png'
        ]);

        $this->assertEquals('/storage/menus/test.png', $menu->image_url);
    }

    /** @test */
    function can_get_price_in_wons()
    {
        $menu = Menu::factory()->make([
            'price' => 5000,
        ]);

        $this->assertEquals('5,000', $menu->price_in_wons);
    }

    /** @test */
    function can_sync_categories()
    {
        $eatery = Eatery::factory()->create()->addCategories([
            ['name' => '추천메뉴'],
            ['name' => '메인메뉴'],
            ['name' => '세트메뉴'],
        ]);

        $menu = Menu::factory()->create()->syncCategories(['추천메뉴', '세트메뉴'], $eatery->id);

        $this->assertCount(2, $menu->categories);
        $this->assertTrue($menu->hasCategoryFor('추천메뉴'));
        $this->assertTrue($menu->hasCategoryFor('세트메뉴'));
        $this->assertFalse($menu->hasCategoryFor('메인메뉴'));
    }
}
