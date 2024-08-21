<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $id = $this->route('id');

        return [
            'item_code' => 'required|string|max:255|unique:products,item_code,' . $id,
            'sku' => 'required|string|max:255|unique:products,sku,' . $id,
            'price' => 'required|numeric',
            'stock' => 'required|numeric',
            'tiers.*.tier_name' => 'required|string|max:255',
            'tiers.*.min_quantity' => 'required|integer|min:0',
            'tiers.*.max_quantity' => 'required|integer|gte:tiers.*.min_quantity',
            'tiers.*.price' => 'nullable|numeric',
        ];
    }

    /**
     * Get custom validation messages.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'tiers.*.max_quantity.gte' => 'Max Quantity must be greater than or equal to Min Quantity.',
        ];
    }
}