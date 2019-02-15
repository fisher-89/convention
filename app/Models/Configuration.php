<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Configuration extends Model
{
    protected $fillable = [
        'round',
        'award_id',
        'persions',
        'is_progress',
    ];

    public function winners()
    {
        return $this->hasMany(Winner::class,'round','round');
    }
}
