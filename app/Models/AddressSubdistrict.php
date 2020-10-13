<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AddressSubdistrict extends Model
{
    public function shippingAddresses()
    {
        return $this->hasMany(ShippingAddress::class);
    }

    public function addressProvince()
    {
        return $this->belongsTo(AddressProvince::class);
    }

    public function addressCity()
    {
        return $this->belongsTo(AddressCity::class);
    }
}
