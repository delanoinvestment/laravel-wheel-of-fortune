<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserInfo extends Model
{
    protected $fillable = [
        'name', 'user_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function defaultShippingAddress()
    {
        return $this->hasOne(ShippingAddress::class, 'id', 'default_shipping_address_id');
    }
}
