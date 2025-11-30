<?php

namespace App\Services\Api\Shop;

use App\Repositories\ProductRepository;
use App\Models\Product;
use App\Services\Api\Shop\ProductImageService;

class ProductService
{
    protected ProductRepository $repository;

    public function __construct(ProductRepository $repository, protected ProductImageService $imageService)
    {
        $this->repository = $repository;
        $this->imageService = $imageService;
    }

    public function listProducts()
    {
        return $this->repository->all();
    }

    public function createProduct(array $data)
    {
        $product = $this->repository->create($data);

        if (isset($data['category_ids'])) {
            $product->categories()->sync($data['category_ids']);
        }
        // 2) آپلود تصاویر (اگر وجود داشت)
        if (!empty($data['images'])) {
            foreach ($data['images'] as $img) {
                $this->imageService->upload($product, $img);
            }
        }

        return $product->load('images');
    }

    public function updateProduct(Product $product, array $data)
    {
        $product = $this->repository->update($product, $data);

        if (isset($data['category_ids'])) {
            $product->categories()->sync($data['category_ids']);
        }

        return $product;
    }

    public function deleteProduct(Product $product)
    {
        return $this->repository->delete($product);
    }
}
