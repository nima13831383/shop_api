<?php

namespace App\Http\Controllers\Api\Shop;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductImage;
use App\Services\Api\Shop\ProductImageService;
use Illuminate\Http\Request;
use App\Http\Requests\StoreProductImageRequest;

class ProductImageController extends Controller
{
    public function __construct(
        protected ProductImageService $service
    ) {}

    public function store(StoreProductImageRequest $request, Product $product)
    {
        $request->validated();
        $image = $this->service->upload($product, $request->file('image'));

        return response()->json([
            'status' => true,
            'data'   => $image
        ]);
    }

    public function destroy(ProductImage $image)
    {
        $this->service->delete($image);

        return response()->json([
            'status' => true,
            'message' => 'Image deleted'
        ]);
    }

    public function setMain(ProductImage $image)
    {
        $img = $this->service->setMain($image);

        return response()->json([
            'status' => true,
            'data'   => $img
        ]);
    }
}
