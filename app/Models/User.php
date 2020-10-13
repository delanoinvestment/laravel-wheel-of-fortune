<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use Notifiable;
    use SoftDeletes;

    const USER_ROLE_ADMIN = 'admin';
    const USER_ROLE_USER = 'user';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'username', 'email', 'password', 'role'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function scopeCustomer($query)
    {
        return $query->where('role', '=', User::USER_ROLE_USER);
    }

    public function scopeVerified($query)
    {
        return $query->whereNotNull('email_verified_at');
    }

    public function info()
    {
        return $this->hasOne(UserInfo::class);
    }

    public function reviews()
    {
        return $this->hasMany(ProductReview::class)->orderBy('created_at', 'DESC');
    }

    public function shoppingCarts()
    {
        return $this->hasMany(ShoppingCart::class)->orderBy('created_at', 'DESC');
    }

    public function getWantedShoppingCarts()
    {
        $shoppingCarts = $this->shoppingCarts;
        if (isset($shoppingCarts)) {
            $items = [];
            foreach ($shoppingCarts as $cart) {
                if ($cart->is_wanted) {
                    $items[] = $cart;
                }
            }
            return $items;
        }
        return null;
    }

    public function countShoppingCartQuantity($orders)
    {
        $qty = 0;
        if (isset($orders)) {
            foreach ($orders as $order) {
                // if ($order->is_wanted) {
                $qty += $order->count;
                // }
            }
        }
        return $qty;
    }
    // public function countShoppingCartQuantity($shoppingCarts)
    // {
    //     $qty = 0;
    //     if (isset($shoppingCarts)) {
    //         foreach ($shoppingCarts as $cart) {
    //             if ($cart->is_wanted) {
    //                 $qty += $cart->count;
    //             }
    //         }
    //     }
    //     return $qty;
    // }

    public function getMidtransCustomerDetails()
    {

        $full_name = $this->info->name;
        $arr = explode(" ", $full_name);
        $count = count($arr);

        if ($count == 1) {
            $first_name = $arr[0];
            $last_name = "";
        } else {
            $first_name = $arr[0];
            $last_name = "";
            for ($i = 1; $i < $count; $i++) {
                $last_name .= $arr[$i];
                if ($i != ($count - 1)) {
                    $last_name .= " ";
                }
            }
        }

        return array(
            "first_name" => $first_name,
            "last_name" => $last_name,
            "email" => $this->email,
            "phone" => $this->info->phone_number,
        );
    }

    public function getWantedShoppingCartQuantityCount()
    {
        $shoppingCarts = $this->getWantedShoppingCarts();
        return $this->countShoppingCartQuantity($shoppingCarts);
    }

    public function countShoppingCartTotalPrice($shoppingCarts)
    {
        $totalPrice = 0;
        if (isset($shoppingCarts)) {
            foreach ($shoppingCarts as $cart) {
                if ($cart->is_wanted) {
                    $totalPrice += $cart->count * $cart->product->price;
                }
            }
        }
        return $totalPrice;
    }

    public function getWantedShoppingCartTotalPrice()
    {
        $shoppingCarts = $this->getWantedShoppingCarts();
        return $this->countShoppingCartTotalPrice($shoppingCarts);
    }

    public function shippingAddresses()
    {
        return $this->hasMany(ShippingAddress::class)->orderBy('created_at', 'DESC');
    }

    public function orders()
    {
        return $this->hasMany(Order::class)->orderBy('created_at', 'DESC');
    }

    public function midtrans()
    {
        return $this->hasMany(Midtran::class);
    }

    public function userPoints()
    {
        return $this->hasMany(UserPoint::class);
    }

    public function pointMutations()
    {
        return $this->hasMany(PointMutation::class);
    }
}
