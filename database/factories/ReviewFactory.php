<?php

namespace Database\Factories;

use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReviewFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Review::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => function () {
                return User::factory()->create()->id;
            },
            'grade' => $this->faker->numberBetween(1, 5),
            'photo_path' => $this->faker->randomElement([null, 'reviews/test.jpeg']),
            'content' => $this->faker->realText($this->faker->numberBetween(10, 50)),
            'eatery_title' => "{$this->faker->word} {$this->faker->city}",
        ];
    }
}
