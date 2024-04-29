<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\ThreeWordName;
use Illuminate\Validation\Rules\File;

class SignUpRequest extends FormRequest
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
            'fullname' => [
                'required',
                new ThreeWordName
            ],
            'email' => [
                'required',
                'email'
            ],
            'phone' => [
                'required',
                'regex:~^[0-9]+$~'
            ],
            'otherphone' => [
                'regex:~^[0-9]+$~'
            ],
            'idimage1' => [
                'required',
                File::image()
            ],
            'idimage2' => [
                'required',
                File::image()
            ],
            'address' => [
                'required',
                'string'
            ],
            'bio' => [
                'required',
                'string'
            ],
            'authimage' => [
                'required',
                File::image()
            ],
            'birthdate' => [
                'required',
                'date'
            ],
            'username' => [
                'required',
                'string'
            ],
            'publicphone' =>[
                'regex:~^[0-9]+$~'
            ],
            'profileimage' => [
                'required', 
                File::image()
            ],
            'password' => [
                'required',
                'string'
            ]
        ];
        
    }
}
