<?php

namespace Tests\Unit;

use App\Models\Option;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class OptionTest extends TestCase
{
    /** @test */
    function can_get_price_in_wons()
    {
        $option = Option::factory()->make([
            'price' => 5000,
        ]);

        $this->assertEquals('5,000', $option->price_in_wons);
    }
}
