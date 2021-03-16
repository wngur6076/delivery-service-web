<?php

namespace Database\Factories;

use App\Models\Menu;
use Illuminate\Database\Eloquent\Factories\Factory;

class MenuFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Menu::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'title' => $this->faker->company,
            'price' => $this->faker->numberBetween(5000, 20000),
            'description' => $this->faker->realText(15),
            'image_path' => config("app.url").'/storage/menus/test.png',
        ];
    }
}
