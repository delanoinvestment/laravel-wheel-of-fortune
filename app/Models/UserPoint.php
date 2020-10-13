<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserPoint extends Model
{
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function pointMutations()
    {
        return $this->hasMany(PointMutation::class);
    }
}
