<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Award extends Model
{
    protected $fillable = [
        'name',
        'url',
    ];
    public $timestamps = false;

    public function getUrlAttribute($value)
    {
        return config('app.url').'/storage'.$value;
    }
}
