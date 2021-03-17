<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Eatery;
use App\Models\Menu;
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
        Eatery::factory(50)->create()->each(function ($eatery) {
            $categories = Category::factory(rand(1, 5))->create(['eatery_id' => $eatery->id]);
            Menu::factory(rand(3, 5))->create()->each(function ($menu) use ($eatery, $categories) {
                $menu->syncCategories($this->faker->randomElements($categories->pluck('name')->toArray(), rand(1, $categories->count())), $eatery->id);
            });
            Review::factory(rand(5, 40))->create(['eatery_title' => $eatery->title]);
        });
    }
}
