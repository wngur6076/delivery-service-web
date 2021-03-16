<?php

namespace Tests\Feature\EateryManagement;

use Tests\TestCase;
use App\Models\Menu;
use App\Models\Eatery;
use App\Models\Review;
use App\Models\Category;
use Illuminate\Http\Testing\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ShowEateryTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function user_can_view_eatery()
    {
        $this->withoutExceptionHandling();
        Storage::fake('public');

        $eatery = Eatery::factory()->create([
            'title' => '만랩커피 강남점',
            'poster_image_path' => $posterImagePath = File::image('eatery-poster.png', 325, 200)->store('posters', 'public'),
            'delivery_time' => '25~50',
            'delivery_charge' => 2000,
            'minimum_order_amount' => 12000,
        ]);
        $eatery->menuCategories()->createMany([
            ['name' => '추천메뉴'],
            ['name' => '메인메뉴'],
            ['name' => '세트메뉴'],
        ]);

        Review::factory(10)->create(['eatery_title' => $eatery->title, 'grade' => 4]);

        Menu::factory()->create([
            'title' => '블랙 피넛 커피',
            'price' => 4800,
            'description' => '피넛을 넣은 커피',
            'image_path' => $menuImagePath = File::image('menu1.png', 325, 200)->store('menus', 'public'),
        ])->categories()->sync(Category::whereIn('name', ['추천메뉴'])->pluck('id'));
        // ->syncCategories(['추천메뉴']);

        $response = $this->json('GET',"api/eateries/{$eatery->id}");

        $response->assertStatus(200);
        $response->assertJson([
            'data' => [
                'id' => 1,
                'title' => '만랩커피 강남점',
                'poster_image' => '/storage/'.$posterImagePath,
                'grade' => 4.0,
                'review_count' => 10,
                'delivery_time' => '25~50',
                'delivery_charge' => '2,000',
                'minimum_order_amount' => '12,000',
                'menu_category' => [
                    [
                        'name' => '추천메뉴',
                        'menus' => [
                            [
                                'id' => 1,
                                'title' => '블랙 피넛 커피',
                                'price' => '4,800',
                                'description' => '피넛을 넣은 커피',
                                'image' => '/storage/'.$menuImagePath,
                            ],
                        ]
                    ],
                    [
                        'name' => '메인메뉴',
                        'menus' => [],
                    ],
                    [
                        'name' => '세트메뉴',
                        'menus' => [],
                    ],
                ]
            ]
        ]);
    }
}
