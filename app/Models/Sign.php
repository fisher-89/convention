<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sign extends Model
{
    protected $fillable = [
        'name',
        'mobile',
        'openid',
        'nickname',
        'avatar',
        'sex',
    ];
}
