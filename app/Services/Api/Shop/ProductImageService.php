<?php

namespace App\Services\Api\Shop;

use App\Models\Product;
use App\Models\ProductImage;
use App\Repositories\ProductImageRepository;
use Illuminate\Support\Facades\Storage;

class ProductImageService
{
    public function __construct(
        protected ProductImageRepository $repo
    ) {}

    public function upload(Product $product, $file)
    {
        $path = $file->store('products', 'public');

        return $this->repo->create([
            'product_id' => $product->id,
            'url'        => $path,
        ]);
    }

    public function delete(ProductImage $image)
    {
        Storage::disk('public')->delete($image->getRawOriginal('url'));

        return $this->repo->delete($image);
    }

    public function setMain(ProductImage $image)
    {
        return $this->repo->setMainImage($image);
    }
}
