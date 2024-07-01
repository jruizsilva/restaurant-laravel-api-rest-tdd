<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRestaurantRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|min:4|max:255',
            'description' => 'required|string|min:4',
            'slug' => 'required|string|unique:restaurants,slug',
        ];
    }

    public function prepareForValidation()
    {
        $this->merge([
            'slug' => str()->slug($this->request->get('name') . "-" . uniqid()),
        ]);
    }
}