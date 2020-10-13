<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PointMutation extends Model
{
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function userPoint()
    {
        return $this->belongsTo(UserPoint::class);
    }
}
