<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Winner extends Model
{
    protected $fillable = [
      'openid'
    ];

    public function sign()
    {
        return $this->hasOne(Sign::class,'openid','openid');
    }
}
