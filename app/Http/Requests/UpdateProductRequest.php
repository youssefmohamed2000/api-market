<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'category_id' => ['required', 'integer', Rule::exists('categories', 'id')],
            'description' => 'required|string',
            'price' => 'required|integer|min:1',
            'quantity' => 'required|integer|min:1',
            'status' => 'nullable|in:0,1',
            'image' => 'nullable|image|mimes:jpeg,png,jpg',
        ];
    }
}
