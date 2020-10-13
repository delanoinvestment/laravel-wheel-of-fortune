<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class AddressCity extends Model
{
    public function shippingAddresses()
    {
        return $this->hasMany(ShippingAddress::class);
    }

    public function addressSubdistricts()
    {
        return $this->hasMany(AddressSubdistrict::class);
    }

    public function addressProvince()
    {
        return $this->belongsTo(AddressProvince::class);
    }
}
