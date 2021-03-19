<?php

namespace Database\Seeders;

use App\Models\Eatery;
use App\Models\Menu;
use App\Models\MenuGroup;
use App\Models\Option;
use App\Models\OptionGroup;
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
            // 옵션그룹 생성 후 옵션들을 연결해준다.
            $optionGroups = OptionGroup::factory(rand(3, 7))->create(['eatery_id' => $eatery->id])->each(function ($optionGroup) {
                Option::factory(rand(1, 8))->create(['option_group_id' => $optionGroup->id]);
                $optionGroup->required = $this->faker->randomElement([true, false, false]);
                $optionGroup->min = $optionGroup->required ? $this->faker->numberBetween(1, $optionGroup->options()->count()) : 0;
                $optionGroup->max = $optionGroup->required ? $this->faker->numberBetween($optionGroup->min, $optionGroup->options()->count())
                    : $this->faker->numberBetween(1, $optionGroup->options()->count());
                $optionGroup->save();
            });

            // 음식점 각각에 메뉴그룹 1~5개 랜덤으로 생성
            MenuGroup::factory(rand(1, 5))->create(['eatery_id' => $eatery->id])->each(function ($menuGroup) use ($optionGroups) {
                // 메뉴그룹 각각에 메뉴 2~6개 랜덤으로 생성
                Menu::factory(rand(2, 6))->create(['menu_group_id' => $menuGroup->id])->each(function ($menu) use ($optionGroups) {
                    // 옵션그룹중에서 랜덤으로 몇개 가져와서 메뉴와 연결한다.
                    $menu->optionGroups()->attach($this->faker->randomElements(
                        $optionGroups->pluck('id')->toArray(),
                        rand(0, $optionGroups->count())
                    ));
                });
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
