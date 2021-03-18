<?php

namespace Tests\Feature\EateryManagement;

use Tests\TestCase;
use App\Models\Menu;
use App\Models\Eatery;
use App\Models\MenuGroup;
use App\Models\Review;
use Illuminate\Http\Testing\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ShowEateryTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function users_can_view_a_single_eatery_with_recommended_menus()
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
        Review::factory(3)->create(['eatery_title' => $eatery->title, 'grade' => 4]);

        $menuGroup1 = MenuGroup::factory()->create(['eatery_id' => $eatery->id, 'name' => '분식메뉴']);
        $menuGroup2 = MenuGroup::factory()->create(['eatery_id' => $eatery->id, 'name' => '한식메뉴']);

        $menu1 = Menu::factory()->create([
            'menu_group_id' => $menuGroup1->id,
            'name' => '블랙 피넛 커피',
            'price' => 4800,
            'description' => '피넛을 넣은 커피',
            'image_path' => $menu1ImagePath = File::image('menu1.png', 325, 200)->store('menus', 'public'),
        ]);
        $menu2 = Menu::factory()->create([
            'menu_group_id' => $menuGroup2->id,
            'name' => '스토리베리 밀크',
            'price' => 3500,
            'description' => null,
            'image_path' => $menu2ImagePath = File::image('menu1.png', 325, 200)->store('menus', 'public'),
        ]);

        $eatery->signatureMenus()->attach($menu1->id);

        $response = $this->json('GET',"api/eateries/{$eatery->id}");

        $response->assertStatus(200);
        $response->assertJson([
            'data' => [
                'id' => 1,
                'title' => '만랩커피 강남점',
                'poster_image' => '/storage/'.$posterImagePath,
                'grade' => 4.0,
                'review_count' => 3,
                'delivery_time' => '25~50',
                'delivery_charge' => '2,000',
                'minimum_order_amount' => '12,000',
                'menu_groups' => [
                    [
                        'name' => '추천메뉴',
                        'menus' => [
                            [
                                'id' => 1,
                                'name' => '블랙 피넛 커피',
                                'price' => '4,800',
                                'description' => '피넛을 넣은 커피',
                                'image' => '/storage/'.$menu1ImagePath,
                            ],
                        ]
                    ],
                    [
                        'name' => '분식메뉴',
                        'menus' => [
                            [
                                'id' => 1,
                                'name' => '블랙 피넛 커피',
                                'price' => '4,800',
                                'description' => '피넛을 넣은 커피',
                                'image' => '/storage/'.$menu1ImagePath,
                            ],
                        ]
                    ],
                    [
                        'name' => '한식메뉴',
                        'menus' => [
                            [
                                'id' => 2,
                                'name' => '스토리베리 밀크',
                                'price' => '3,500',
                                'description' => null,
                                'image' => '/storage/'.$menu2ImagePath,
                            ],
                        ]
                    ],
                ]
            ]
        ]);
    }

    /** @test */
    function users_can_view_a_single_eatery_with_no_recommended_menus()
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
        Review::factory(3)->create(['eatery_title' => $eatery->title, 'grade' => 4]);

        $menuGroup1 = MenuGroup::factory()->create(['eatery_id' => $eatery->id, 'name' => '분식메뉴']);
        $menuGroup2 = MenuGroup::factory()->create(['eatery_id' => $eatery->id, 'name' => '한식메뉴']);

        $menu1 = Menu::factory()->create([
            'menu_group_id' => $menuGroup1->id,
            'name' => '블랙 피넛 커피',
            'price' => 4800,
            'description' => '피넛을 넣은 커피',
            'image_path' => $menu1ImagePath = File::image('menu1.png', 325, 200)->store('menus', 'public'),
        ]);
        $menu2 = Menu::factory()->create([
            'menu_group_id' => $menuGroup2->id,
            'name' => '스토리베리 밀크',
            'price' => 3500,
            'description' => null,
            'image_path' => $menu2ImagePath = File::image('menu1.png', 325, 200)->store('menus', 'public'),
        ]);

        // $eatery->signatureMenus()->attach($menu1->id);

        $response = $this->json('GET',"api/eateries/{$eatery->id}");

        $response->assertStatus(200);
        $response->assertJsonCount(2, 'data.menu_groups');
        $response->assertJsonMissingExact(
            [
                'name' => '추천메뉴',
                'menus' => [
                    [
                        'id' => 1,
                        'name' => '블랙 피넛 커피',
                        'price' => '4,800',
                        'description' => '피넛을 넣은 커피',
                        'image' => '/storage/'.$menu1ImagePath,
                    ]
                ]
            ]
        );
        $this->assertTrue($eatery->signatureMenus->isEmpty());
    }
}
