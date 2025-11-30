<?php

namespace App\Repositories;

use App\Models\ProductImage;

class ProductImageRepository
{
    public function create(array $data)
    {
        return ProductImage::create($data);
    }

    public function delete(ProductImage $image)
    {
        return $image->delete();
    }

    public function setMainImage(ProductImage $image)
    {
        $image->product->images()->update(['is_main' => false]);

        $image->update(['is_main' => true]);

        return $image;
    }
}
