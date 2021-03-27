<?php

namespace Tests\Unit;

use Mockery;
use Tests\TestCase;
use App\Models\Eatery;
use App\Models\Option;
use App\Models\OptionGroup;
use App\Exceptions\OptionCountException;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class OptionGroupTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function min_validation_can_be_done()
    {
        $this->expectException(OptionCountException::class);

        $eatery = Mockery::mock(Eatery::class);
        $eatery->shouldReceive('getAttribute')->with('id')->andReturn(1);

        $optionGroup = OptionGroup::factory()->create([
            'eatery_id' => $eatery->id,
            'required' => true,
            'min' => 2,
            'max' => 2,
        ]);

        $option1 = Option::factory()->create([
            'option_group_id' => $optionGroup->id,
        ]);
        $option2 = Option::factory()->create([
            'option_group_id' => $optionGroup->id,
        ]);

        $optionGroup->optionCountValidation([$option1->id]);
    }

    /** @test */
    public function max_validation_can_be_done()
    {
        $this->expectException(OptionCountException::class);

        $eatery = Mockery::mock(Eatery::class);
        $eatery->shouldReceive('getAttribute')->with('id')->andReturn(1);

        $optionGroup = OptionGroup::factory()->create([
            'eatery_id' => $eatery->id,
            'required' => false,
            'min' => 0,
            'max' => 2,
        ]);

        $option1 = Option::factory()->create([
            'option_group_id' => $optionGroup->id,
        ]);
        $option2 = Option::factory()->create([
            'option_group_id' => $optionGroup->id,
        ]);
        $option3 = Option::factory()->create([
            'option_group_id' => $optionGroup->id,
        ]);

        $optionGroup->optionCountValidation([$option2->id, $option1->id, $option3->id]);
    }
}
