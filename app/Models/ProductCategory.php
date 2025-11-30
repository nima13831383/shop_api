<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductCategory extends Model
{
    //use SoftDeletes;
    protected $fillable = [
        'name',
        'slug',
        'parent_id',
    ];

    /* parent category */
    public function parent()
    {
        return $this->belongsTo(ProductCategory::class, 'parent_id');
    }

    /* sub categories */
    public function children()
    {
        return $this->hasMany(ProductCategory::class, 'parent_id');
    }

    /* products in category */
    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_category', 'category_id', 'product_id');
    }
}
