<?php

namespace App\Http\Controllers\Api\Shop;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductReviewRequest;
use App\Http\Requests\UpdateProductReviewRequest;
use App\Models\ProductReview;
use App\Services\Api\Shop\ProductReviewService;
use Symfony\Component\HttpFoundation\Request;

class ProductReviewController extends Controller
{
    public function __construct(
        protected ProductReviewService $service
    ) {}

    public function index($productId)
    {
        $reviews = $this->service->listByProduct($productId);

        return response()->json([
            'status' => true,
            'data'   => $reviews
        ]);
    }

    public function store(StoreProductReviewRequest $request)
    {
        $data = $request->validated();
        $data['user_id'] = $request->user()->id;

        $review = $this->service->store($data);

        return response()->json([
            'status' => true,
            'data'   => $review
        ], 201);
    }

    public function update(UpdateProductReviewRequest $request, ProductReview $review)
    {
        $review = $this->service->update($review, $request->validated());

        return response()->json([
            'status' => true,
            'data'   => $review
        ]);
    }

    public function destroy(ProductReview $review)
    {
        $this->service->delete($review);
        return response()->json([
            'status' => true,
            'message' => 'Review deleted'
        ]);
    }
    public function approve(ProductReview $review)
    {
        return $this->service->approve($review);
    }
    public function reject(ProductReview $review)
    {
        return $this->service->reject($review);
    }
}
