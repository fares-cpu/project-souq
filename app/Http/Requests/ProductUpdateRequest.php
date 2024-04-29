<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\File;

class ProductUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'description' => [
                'string'
            ],
            'mainimage' => [
                File::image()
            ],
            'image2' => [
                File::image()
            ],
            'image3' => [
                File::image()
            ],
            'image4' => [
                File::image()
            ],
            'price' => [
                'numeric'
            ],
            'instock' => [
                'numeric'
            ]
        
        ];
    }
}
