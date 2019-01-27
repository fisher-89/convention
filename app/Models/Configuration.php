<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Configuration extends Model
{
    protected $fillable = [
        'round',
        'award_id',
        'persions'
    ];

    public function winners()
    {
        return $this->hasMany(Winner::class,'round','round')->where('is_receive',1);
    }
}
