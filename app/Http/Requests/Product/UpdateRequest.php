<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
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
        return [
            'title' => 'nullable|string|min:30|max:255',
            'description' => 'nullable|string',
            'price' => 'nullable|numeric|min:1',
            'stock_quantity' => 'nullable|integer|min:1',
            'pictures' => 'nullable|array',
            'pictures.*' => 'string',
        ];
    }
}
