<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Winner extends Model
{
    protected $fillable = [
        'openid',
        'award_id',
        'is_receive',
        'round',
    ];

    public function sign()
    {
        return $this->hasOne(Sign::class, 'openid', 'openid');
    }

    public function award()
    {
        return $this->belongsTo(Award::class);
    }
}
