<?php

namespace App\Repositories;

use App\Models\ProductCategory;

class ProductCategoryRepository
{
    public function getAll()
    {
        return ProductCategory::with('parent')->get();
    }

    public function findOrFail($id)
    {
        return ProductCategory::with('parent')->findOrFail($id);
    }

    public function create(array $data)
    {
        return ProductCategory::create($data);
    }

    public function update($id, array $data)
    {
        $category = ProductCategory::findOrFail($id);
        $category->update($data);
        return $category;
    }

    public function delete($id)
    {
        $category = ProductCategory::findOrFail($id);
        return $category->delete();
    }
}
