<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'title',
        'slug',
        'description',
        'price',
        'discount_price',
        'stock',
        'status',
    ];

    /* product images */
    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }

    // برای گرفتن URL همه تصاویر
    public function getImageUrlsAttribute()
    {
        return ['images' => $this->images->map(fn($img) => ['id' => $img->id, 'url' => asset('storage/' . $img->url)])];
    }

    /* categories */
    public function categories()
    {
        return $this->belongsToMany(ProductCategory::class, 'product_category', 'product_id', 'category_id');
    }


    /* reviews */
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    /* average rating */
    public function avgRating()
    {
        return $this->reviews()->avg('rating');
    }
}
