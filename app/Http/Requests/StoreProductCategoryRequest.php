<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // یا بررسی ادمین
    }

    public function rules(): array
    {
        return [
            'name'      => 'required|string|max:255',
            'slug'      => 'required|string|max:255|unique:product_categories,slug',
            'parent_id' => 'nullable|exists:product_categories,id',
        ];
    }
}
