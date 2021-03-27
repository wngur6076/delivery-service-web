<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Exceptions\OptionCountException;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OptionGroup extends Model
{
    use HasFactory;

    public function options()
    {
        return $this->hasMany(Option::class);
    }

    public function optionCountValidation($optionIds = [])
    {
        $selectOptionCount = Option::whereIn('id', $optionIds)->where('option_group_id', $this->id)->count();
        if ($this->min > $selectOptionCount || $this->max < $selectOptionCount) {
            throw new OptionCountException;
        }
    }
}
