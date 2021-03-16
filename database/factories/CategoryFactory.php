<?php

namespace Database\Factories;

use App\Models\Eatery;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class CategoryFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Category::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'eatery_id' => function () {
                return Eatery::factory()->create()->id;
            },
            'name' => $this->faker->companySuffix,
        ];
    }
}
