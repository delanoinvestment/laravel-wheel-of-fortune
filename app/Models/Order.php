<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    const SELLER_CONFIRMATION_PERIOD_DAY = 1;

    const STATUSES = [
        'UNFINISHED', // still unfinished
        'PENDING', // waiting for further actions
        'PROGRESS', // delivery is in progress (internally)
        'DISPATCHED', // parcel was handed over to the logistic partner
        'DELIVERED', // customer received the parcel
        'LOST', // parcel is lost on the way to the customer
        'REFUSED', // parcel was refused by the customer
        'RETURNED', // parcel was returned to the shop owner
        'CANCELLED', // customer cancelled the parcel
        'COMPLETED' // customer completed the order
    ];

    protected $fillable = [
        'transaction_code',
        'status',
        'is_seller_confirmed',
        'seller_confirmation_expired_at',
        'shipping_address_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function invoice()
    {
        return $this->hasOne(Invoice::class);
    }

    public function shippingAddress()
    {
        return $this->belongsTo(ShippingAddress::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function getTotalPriceOfItems()
    {
        $items = $this->items;
        if (isset($items)) {
			$total = 0;
			foreach ($items as $item) {
                $total += $item->total_price;
            }
			return $total;
		}
		return 0;
    }

    public function getTotalPointsOfItems()
    {
        $items = $this->items;
        if (isset($items)) {
			$total = 0;
			foreach ($items as $item) {
                $total += $item->total_point;
            }
			return $total;
		}
        return 0;
    }

}
