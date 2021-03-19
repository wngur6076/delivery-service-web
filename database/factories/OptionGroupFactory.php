<?php

namespace Database\Factories;

use App\Models\OptionGroup;
use Illuminate\Database\Eloquent\Factories\Factory;

class OptionGroupFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = OptionGroup::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $required = $this->faker->randomElement([false, true]);

        return [
            'name' => "{$this->faker->companySuffix} {$this->faker->regexify('[A-Z0-9]')}",
            'required' => $required,

        ];
    }
}

/* $table->unsignedBigInteger('menu_id')->nullable();
$table->string('name');
$table->boolean('required');
$table->integer('min');
$table->integer('max');
$table->integer('option_count');
 */
