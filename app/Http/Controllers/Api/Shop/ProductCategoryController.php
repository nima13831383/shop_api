<?php

namespace App\Http\Controllers\Api\Shop;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductCategoryRequest;
use App\Http\Requests\UpdateProductCategoryRequest;
use App\Services\Api\Shop\ProductCategoryService;
use Illuminate\Http\JsonResponse;

class ProductCategoryController extends Controller
{
    protected ProductCategoryService $service;

    public function __construct(ProductCategoryService $service)
    {
        $this->service = $service;
    }

    // GET /product-categories
    public function index(): JsonResponse
    {
        return response()->json([
            'status' => true,
            'data'   => $this->service->getAll(),
        ]);
    }

    // POST /product-categories
    public function store(StoreProductCategoryRequest $request): JsonResponse
    {
        $category = $this->service->create($request->validated());

        return response()->json([
            'status' => true,
            'message' => 'Category created successfully.',
            'data'   => $category
        ], 201);
    }

    // GET /product-categories/{id}
    public function show($id): JsonResponse
    {
        $category = $this->service->find($id);

        return response()->json([
            'status' => true,
            'data'   => $category,
        ]);
    }

    // PUT /product-categories/{id}
    public function update(UpdateProductCategoryRequest $request, $id): JsonResponse
    {
        $category = $this->service->update($id, $request->validated());

        return response()->json([
            'status' => true,
            'message' => 'Category updated successfully.',
            'data'   => $category
        ]);
    }

    // DELETE /product-categories/{id}
    public function destroy($id): JsonResponse
    {
        $this->service->delete($id);

        return response()->json([
            'status' => true,
            'message' => 'Category deleted successfully.',
        ]);
    }
}
