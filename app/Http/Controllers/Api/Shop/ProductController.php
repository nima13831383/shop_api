<?php

namespace App\Http\Controllers\Api\Shop;

use App\Http\Controllers\Controller;
use App\Services\Api\Shop\ProductService;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Product;

class ProductController extends Controller
{
    public function __construct(
        protected ProductService $service
    ) {}

    public function index()
    {
        return response()->json([
            'status' => true,
            'data'   => $this->service->listProducts()
        ]);
    }

    public function store(StoreProductRequest $request)
    {
        $product = $this->service->createProduct($request->validated());

        return response()->json([
            'status' => true,
            'data'   => $product
        ], 201);
    }

    public function show(Product $product)
    {
        return response()->json([
            'status' => true,
            'data'   => $product->load(['images', 'categories'])
        ]);
    }

    public function update(UpdateProductRequest $request, Product $product)
    {
        $product = $this->service->updateProduct($product, $request->validated());

        return response()->json([
            'status' => true,
            'data'   => $product
        ]);
    }

    public function destroy(Product $product)
    {
        $this->service->deleteProduct($product);

        return response()->json([
            'status' => true,
            'message' => 'Product deleted.'
        ]);
    }
}
