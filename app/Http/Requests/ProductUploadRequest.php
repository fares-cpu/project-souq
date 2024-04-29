<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\File;

class ProductUploadRequest extends FormRequest
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
            'name' => [
                'required',
                'string',
                'regex:/^[A-Za-z\s]*$|^[\x{0600}-\x{06FF}\s]*$/u'
            ],
            'description' => [
                'required',
                'string'
            ],
            'mainimage' => [
                'required',
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
            'category' => [
                'required',
                'string'
            ],
            'price' => [
                'required',
                'numeric'
            ],
            'instock' => [
                'required',
                'numeric'
            ]
        ];
    }
}