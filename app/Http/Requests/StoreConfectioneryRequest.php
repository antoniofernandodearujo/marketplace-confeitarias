<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreConfectioneryRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'latitude' => ['required_if:has_map,true', 'numeric', 'between:-90,90'],
            'longitude' => ['required_if:has_map,true', 'numeric', 'between:-180,180'],
            'phone' => ['required', 'string', 'max:20'],
            'address.cep' => ['required', 'string', 'size:8'],
            'address.street' => ['required', 'string', 'max:255'],
            'address.number' => ['required', 'string', 'max:20'],
            'address.neighborhood' => ['required', 'string', 'max:100'],
            'address.state' => ['required', 'string', 'size:2'],
            'address.city' => ['required', 'string', 'max:100'],
        ];
    }
}