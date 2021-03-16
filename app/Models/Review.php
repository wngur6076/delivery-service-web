<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    public static function boot()
    {
        parent::boot();

        static::created(function($review) {
            $eatery = Eatery::findByTitle($review->eatery_title);
            $eatery->increment('review_count');
            $eatery->update(['grade' => $eatery->gradeAverage()]);
        });

        static::deleted(function($review) {
            $eatery = Eatery::findByTitle($review->eatery_title);
            $eatery->decrement('review_count');
            $eatery->update(['grade' => $eatery->gradeAverage()]);
        });
    }
}
