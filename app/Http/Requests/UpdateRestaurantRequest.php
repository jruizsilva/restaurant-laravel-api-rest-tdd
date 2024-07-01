<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateRestaurantRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check() && auth()->user()->id === $this->restaurant->user_id;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            "name" => "required|string|min:4|max:100",
            "description" => "required|string|min:4",
            "slug" => "required|string|min:4|max:100|unique:restaurants,slug," . $this->restaurant->id,
        ];
    }

    public function prepareForValidation()
    {
        $slug = $this->restaurant->slug;
        if ($this->request->get('name') !== $this->restaurant->name) {
            $slug = str($this->name . "-" . uniqid())->slug()->toString();
        }
        $this->merge([
            "slug" => $slug,
        ]);
    }
}