<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Eatery;
use App\Models\Review;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class EateryTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function can_get_poster_image_url()
    {
        Storage::fake('public');

        $eatery = Eatery::factory()->make([
            'poster_image_path' => 'posters/test.png'
        ]);

        dd($eatery->poster_image_url);
        $this->assertEquals('/storage/posters/test.png', $eatery->poster_image_url);
    }

    /** @test */
    function can_get_delivery_charge_in_wons()
    {
        $eatery = Eatery::factory()->make([
            'delivery_charge' => 5000,
        ]);

        $this->assertEquals('5,000', $eatery->delivery_charge_in_wons);
    }

    /** @test */
    function can_get_minimum_order_amount_in_wons()
    {
        $eatery = Eatery::factory()->make([
            'minimum_order_amount' => 12000,
        ]);

        $this->assertEquals('12,000', $eatery->minimum_order_amount_in_wons);
    }

    /** @test */
    function can_see_the_average_of_the_grades()
    {
        $eatery = Eatery::factory()->create(['title' => '만랩커피 강남점']);

        Review::factory()->create(['grade' => 5, 'eatery_title' => $eatery->title]);
        Review::factory()->create(['grade' => 4, 'eatery_title' => $eatery->title]);
        Review::factory()->create(['grade' => 1, 'eatery_title' => $eatery->title]);

        $this->assertEquals(3.3, $eatery->gradeAverage());
    }

    /** @test */
    function review_is_created_it_automatically_calculates_the_grade()
    {
        $eatery = Eatery::factory()->create(['title' => '만랩커피 강남점']);

        Review::factory()->create(['grade' => 5, 'eatery_title' => $eatery->title]);
        Review::factory()->create(['grade' => 4, 'eatery_title' => $eatery->title]);
        Review::factory()->create(['grade' => 1, 'eatery_title' => $eatery->title]);

        $this->assertEquals(3.3, $eatery->fresh()->grade);
    }

    /** @test */
    function review_is_deleted_it_automatically_calculates_the_grade()
    {
        $eatery = Eatery::factory()->create(['title' => '만랩커피 강남점']);

        Review::factory()->create(['grade' => 5, 'eatery_title' => $eatery->title]);
        Review::factory()->create(['grade' => 4, 'eatery_title' => $eatery->title]);
        Review::factory()->create(['grade' => 1, 'eatery_title' => $eatery->title]);
        $this->assertEquals(3.3, $eatery->fresh()->grade);

        Review::destroy([1, 3]);
        $this->assertEquals(4, $eatery->fresh()->grade);
    }

    /** @test */
    function review_is_created_the_number_of_reviews_is_automatically_increased()
    {
        $eatery = Eatery::factory()->create(['title' => '만랩커피 강남점']);

        Review::factory(4)->create(['eatery_title' => $eatery->title]);
        $this->assertEquals(4, $eatery->fresh()->review_count);
        Review::factory(7)->create(['eatery_title' => $eatery->title]);
        $this->assertEquals(11, $eatery->fresh()->review_count);
    }

    /** @test */
    function review_is_deleted_the_number_of_reviews_is_automatically_decreased()
    {
        $eatery = Eatery::factory()->create(['title' => '만랩커피 강남점']);

        Review::factory(10)->create(['eatery_title' => $eatery->title]);
        $this->assertEquals(10, $eatery->fresh()->review_count);

        Review::destroy([1, 2, 3]);

        $this->assertEquals(7, $eatery->fresh()->review_count);
    }

    /** @test */
    function retrieving_an_eatery_by_title()
    {
        $eatery = Eatery::factory()->create([
            'title' => '만랩커피 강남점',
        ]);

        $foundEatery = Eatery::findByTitle('만랩커피 강남점');

        $this->assertEquals($eatery->id, $foundEatery->id);
    }

    /** @test */
    function retrieving_a_nonexistent_eatery_by_title_throws_an_exception()
    {
        $this->expectException(ModelNotFoundException::class);
        Eatery::findByTitle('NONEXISTENTTITLE');
    }
}
