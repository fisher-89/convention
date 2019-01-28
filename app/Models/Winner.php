<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Winner extends Model
{
    protected $fillable = [
        'openid',
        'is_receive',
        'round',
    ];

    public function sign()
    {
        return $this->hasOne(Sign::class, 'openid', 'openid');
    }

    public function configuration()
    {
        return $this->belongsTo(Configuration::class,'round','round');
    }
}
