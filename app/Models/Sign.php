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
        'number',
        'hotel_name',
        'hotel_num',
        'idcard',
        'start_time',
        'end_time',
        'money',
        'update_staff',
        'update_name',
    ];
}
