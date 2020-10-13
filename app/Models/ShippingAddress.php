<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Models\AddressSubdistrict;

class ShippingAddress extends Model
{
    use SoftDeletes;

    protected $fillable = ['label', 'recipient', 'phone_number', 'country', 'zip_code', 'address', 'additional_info', 'address_subdistrict_id', 'user_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function addressSubdistrict()
    {
        return $this->belongsTo(AddressSubdistrict::class);
    }

    public function getProvince()
    {
        return $this->addressSubdistrict->addressProvince;
    }

    public function getCity()
    {
        return $this->addressSubdistrict->addressCity;
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function getAdministrativeDivisionDetail()
    {
        return $this->getProvince()->name. ", " . $this->getCity()->name . ", " . $this->addressSubdistrict->name;
    }

    public function getFullAddress()
    {
        return $this->address.', '.$this->getAdministrativeDivisionDetail().', '.$this->zip_code;
    }

}
