<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\App;

use App\Traits\HasImages;

class OrderItem extends Model
{
    use SoftDeletes;
    use HasImages;

    protected $fillable = [
        'name',
        'count',
        'unit_price',
        'total_price',
        'unit_point',
        'total_point',
        'product_id',
    ];

    protected $casts = [
        'name' => 'array',
    ];

    protected $appends = [
        'translated_name',
    ];

    public function product() {
        return $this->belongsTo(Product::class);
    }

    public function order() {
        return $this->belongsTo(Order::class);
    }

    public function shoppingCart() {
        return $this->hasOne(ShoppingCart::class);
    }

    public function getTranslatedNameAttribute()
    {
        if (!is_array($this->name)) $name = json_decode($this->name, true);
        else $name = $this->name;
        return $name[App::getLocale()];
    }

    public function getLocalisedNameAttribute($locale)
    {
        if (!is_array($this->name)) $name = json_decode($this->name, true);
        else $name = $this->name;
        return $name[$locale];
    }

    public function getMainImageCollection()
    {
        $images = $this->images;
        if (isset($images)) {
			$items = [];
			foreach ($images as $image) {
				if ($image->collection_name == Product::IMAGE_COLLECTION_MAIN) {
					$items[] = $image;
				}
            }
			return $items;
		}
		return null;
    }

    public function getOverviewImageCollection()
    {
        $images = $this->images;
        if (isset($images)) {
			$items = [];
			foreach ($images as $image) {
				if ($image->collection_name == Product::IMAGE_COLLECTION_OVERVIEW) {
					$items[] = $image;
				}
            }
			return $items;
		}
		return null;
    }

    public function getFooterImage()
    {
        $images = $this->images;
        if (isset($images)) {
			foreach ($images as $image) {
				if ($image->collection_name == Product::IMAGE_COLLECTION_FOOTER) {
					return $image;
				}
            }
			return null;
		}
		return null;
    }

    public function getCarouselImageCollection()
    {
        $images = $this->images;
        if (isset($images)) {
			$items = [];
			foreach ($images as $image) {
				if ($image->collection_name == Product::IMAGE_COLLECTION_CAROUSEL) {
					$items[] = $image;
				}
            }
			return $items;
		}
		return null;
    }
}
