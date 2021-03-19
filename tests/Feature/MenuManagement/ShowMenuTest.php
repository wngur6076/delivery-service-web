<?php

namespace Tests\Feature\MenuManagement;

use Tests\TestCase;
use App\Models\Menu;
use App\Models\Eatery;
use App\Models\Option;
use App\Models\MenuGroup;
use App\Models\OptionGroup;
use Illuminate\Http\Testing\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;

class ShowMenuTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_view_their_menu()
    {
        $this->withoutExceptionHandling();
        Storage::fake('public');

        // $eatery = Eatery::factory()->create();
        // $menuGroup = MenuGroup::factory()->create(['eatery_id' => $eatery->id]);

        $eatery = Mockery::mock(Eatery::class);
        $eatery->shouldReceive('getAttribute')->with('id')->andReturn(1);

        $menuGroup = Mockery::mock(MenuGroup::class);
        $menuGroup->shouldReceive('getAttribute')->with('id')->andReturn(1);

        $menu = Menu::factory()->create([
            'menu_group_id' => $menuGroup->id,
            'name' => '블랙 피넛 커피',
            'price' => 4800,
            'description' => '피넛을 넣은 커피',
            'image_path' => $menuImagePath = File::image('menu1.png', 325, 200)->store('menus', 'public'),
        ]);

        $optionGroup1 = OptionGroup::factory()->create([
            'eatery_id' => $eatery->id,
            'name' => '맛 선택',
            'required' => true,
            'min' => 1,
            'max' => 1,
        ]);
        Option::factory()->create([
            'option_group_id' => $optionGroup1->id,
            'name' => '순한맛',
            'price' => 0,
        ]);
        Option::factory()->create([
            'option_group_id' => $optionGroup1->id,
            'name' => '보통맛',
            'price' => 1000,
        ]);
        Option::factory()->create([
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

        Option::factory()->create([
            'option_group_id' => $optionGroup2->id,
            'name' => '스콘',
            'price' => 1000,
        ]);
        Option::factory()->create([
            'option_group_id' => $optionGroup2->id,
            'name' => '쿠키',
            'price' => 1500,
        ]);

        $menu->optionGroups()->attach([$optionGroup1->id, $optionGroup2->id]);

        $response = $this->json('GET',"api/menus/{$menu->id}");

        $response->assertStatus(200);
        $response->assertJson([
            'data' => [
                'id' => 1,
                'name' => '블랙 피넛 커피',
                'price' => '4,800',
                'description' => '피넛을 넣은 커피',
                'image' => '/storage/'.$menuImagePath,
                'option_groups' => [
                    [
                        'name' => '맛 선택',
                        'required' => true,
                        'min' => 1,
                        'max' => 1,
                        'option_count' => 3,
                        'options' => [
                            [
                                'id' => 1,
                                'name' => '순한맛',
                                'price' => '0',
                            ],
                            [
                                'id' => 2,
                                'name' => '보통맛',
                                'price' => '1,000',
                            ],
                            [
                                'id' => 3,
                                'name' => '매운맛',
                                'price' => '2,000',
                            ],
                        ]
                    ],
                    [
                        'name' => '추가 선택',
                        'required' => false,
                        'min' => 0,
                        'max' => 2,
                        'option_count' => 2,
                        'options' => [
                            [
                                'id' => 4,
                                'name' => '스콘',
                                'price' => '1,000',
                            ],
                            [
                                'id' => 5,
                                'name' => '쿠키',
                                'price' => '1,500',
                            ],
                        ]
                    ],
                ]
            ]
        ]);
    }
}
