<?php

namespace App\Repositories;

use App\Models\ProductReview;

class ProductReviewRepository
{
    public function create(array $data): ProductReview
    {
        return ProductReview::create($data);
    }

    public function update(ProductReview $review, array $data): ProductReview
    {
        $review->update($data);
        return $review;
    }

    public function delete(ProductReview $review): bool
    {
        return $review->delete();
    }

    public function productReviews(int $productId)
    {
        return ProductReview::with('user')
            ->where('product_id', $productId)
            ->latest()
            ->paginate(10);
    }
}
