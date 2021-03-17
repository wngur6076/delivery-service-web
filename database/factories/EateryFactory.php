<?php

namespace Database\Factories;

use App\Models\Eatery;
use Illuminate\Database\Eloquent\Factories\Factory;

class EateryFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Eatery::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $deliveryTimeStart = $this->faker->numberBetween(10, 40);
        return [
            'title' => "{$this->faker->word} {$this->faker->city} {$this->faker->randomDigit}",
            'poster_image_path' => 'posters/test.jpeg',
            'delivery_time' => "{$deliveryTimeStart}~{$this->faker->numberBetween($deliveryTimeStart+10, $deliveryTimeStart+40)}",
            'delivery_charge' => $this->faker->randomNumber(4),
            'minimum_order_amount' => $this->faker->randomElement([5000, 9500, 12000, 15000, 20000]),
        ];
    }
}
