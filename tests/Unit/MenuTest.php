<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Menu;
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
}
