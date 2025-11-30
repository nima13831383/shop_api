<?php

namespace App\Services\Api\Shop;

use App\Repositories\ProductCategoryRepository;

class ProductCategoryService
{
    protected ProductCategoryRepository $repo;

    public function __construct(ProductCategoryRepository $repo)
    {
        $this->repo = $repo;
    }

    public function getAll()
    {
        return $this->repo->getAll();
    }

    public function find($id)
    {
        return $this->repo->findOrFail($id);
    }

    public function create(array $data)
    {
        return $this->repo->create($data);
    }

    public function update($id, array $data)
    {
        return $this->repo->update($id, $data);
    }

    public function delete($id)
    {
        return $this->repo->delete($id);
    }
}
