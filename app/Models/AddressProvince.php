<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class AddressProvince extends Model
{
    public $timestamps = false;

    public function shippingAddresses()
    {
        return $this->hasMany(ShippingAddress::class);
    }

    public function addressCities()
    {
        return $this->hasMany(AddressCity::class);
    }

    public function addressSubdistricts()
    {
        return $this->hasMany(AddressSubdistrict::class);
    }
}
