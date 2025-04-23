<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0'],
            'description' => ['nullable', 'string'],
            'confectionery_id' => ['required', 'exists:confectioneries,id'],
            'images' => ['nullable', 'array'],
            'images.*' => ['nullable', 'image', 'max:2048'] // 2MB max
        ];
    }
}