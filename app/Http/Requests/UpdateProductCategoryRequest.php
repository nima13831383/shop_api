<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'      => 'sometimes|string|max:255',
            'slug'      => 'sometimes|string|max:255|unique:product_categories,slug,' . $this->id,
            'parent_id' => 'nullable|exists:product_categories,id',
        ];
    }
}
