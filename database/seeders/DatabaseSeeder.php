<?php

namespace Database\Seeders;

use App\Models\Eatery;
use App\Models\Menu;
use App\Models\MenuGroup;
use App\Models\Review;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    protected $faker;

    public function __construct()
    {
        $this->faker = \Faker\Factory::create();
    }

    public function run()
    {
        // 음식점 50개 생성
        Eatery::factory(50)->create()->each(function ($eatery) {
            // 음식점 각각에 메뉴그룹 1~5개 랜덤으로 생성
            MenuGroup::factory(rand(1, 5))->create(['eatery_id' => $eatery->id])->each(function ($menuGroup) {
                // 메뉴그룹 각각에 메뉴 3~6개 랜덤으로 생성
                Menu::factory(rand(2, 6))->create(['menu_group_id' => $menuGroup->id]);
            });

            // 메뉴만 보이게 merge한다.
            $item = collect();
            foreach ($eatery->menuGroups->pluck('menus') as $menuGroup) {
                $item = $item->merge($menuGroup);
            }

            // 메뉴중에서 랜덤으로 메뉴를 가져와서 대표메뉴를 선택한다. (대표메뉴는 최대 6개 까지 가능)
            $eatery->signatureMenus()->attach($this->faker->randomElements(
                $item->pluck('id')->toArray(),
                rand(0, $item->count() > 6 ? 6 : $item->count())
            ));

            // 음식점 각각에 리뷰를 5~40 렌담으로 생성
            Review::factory(rand(5, 40))->create(['eatery_title' => $eatery->title]);
        });
    }
}
