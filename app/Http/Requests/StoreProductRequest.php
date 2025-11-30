<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title'          => 'required|string|max:255',
            'slug'           => 'required|string|unique:products,slug',
            'description'    => 'nullable|string',
            'price'          => 'required|numeric',
            'discount_price' => 'nullable|numeric',
            'stock'          => 'required|integer|min:0',
            'status'         => 'required|in:active,inactive',
            'category_ids'   => 'nullable|array',
            'category_ids.*' => 'exists:product_categories,id',
            // تصاویر همزمان با ساخت
            'images'   => 'nullable|array',
            'images.*' => 'image|max:4096'
        ];
    }
}
