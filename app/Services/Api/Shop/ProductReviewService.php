<?php

namespace App\Services\Api\Shop;

use App\Repositories\ProductReviewRepository;
use App\Models\ProductReview;

class ProductReviewService
{
    public function __construct(
        protected ProductReviewRepository $repo
    ) {}

    public function store(array $data)
    {
        return $this->repo->create($data);
    }

    public function update(ProductReview $review, array $data)
    {
        return $this->repo->update($review, $data);
    }

    public function delete(ProductReview $review)
    {
        return $this->repo->delete($review);
    }

    public function listByProduct(int $productId)
    {
        return $this->repo->productReviews($productId);
    }
    public function approve(ProductReview $review)
    {
        return $this->repo->update($review, ['status' => 'approved']);
    }

    public function reject(ProductReview $review)
    {
        return $this->repo->update($review, ['status' => 'unapproved']);
    }
}
