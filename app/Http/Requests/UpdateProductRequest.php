<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title'          => 'sometimes|string|max:255',
            'slug'           => 'sometimes|string|unique:products,slug,' . $this->product->id,
            'description'    => 'nullable|string',
            'price'          => 'sometimes|numeric',
            'discount_price' => 'nullable|numeric',
            'stock'          => 'sometimes|integer|min:0',
            'status'         => 'sometimes|in:active,inactive',
            'category_ids'   => 'nullable|array',
            'category_ids.*' => 'exists:product_categories,id',
            // تصاویر همزمان با ساخت
            'images'   => 'nullable|array',
            'images.*' => 'image|max:4096'
        ];
    }
}
