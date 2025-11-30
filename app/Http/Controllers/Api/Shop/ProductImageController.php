<?php

namespace App\Http\Controllers\Api\Shop;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductImage;
use App\Services\Api\Shop\ProductImageService;
use Illuminate\Http\Request;

class ProductImageController extends Controller
{
    public function __construct(
        protected ProductImageService $service
    ) {}

    public function store(Request $request, Product $product)
    {
        $request->validate([
            'image' => 'required|image|max:2048'
        ]);

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
