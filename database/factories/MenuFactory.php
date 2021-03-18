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
            'name' => $this->faker->company,
            'price' => $this->faker->numberBetween(5000, 20000),
            'description' => $this->faker->randomElement([$this->faker->realText($this->faker->numberBetween(10, 25)), null]),
            'image_path' => 'menus/test.jpeg',
        ];
    }
}
